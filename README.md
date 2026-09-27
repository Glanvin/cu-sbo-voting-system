# SBO Voting System

A student-body-officer voting system, converted from the original procedural
PHP application to CodeIgniter 3.

The pre-conversion implementation is kept, untouched, in [`legacy/`](legacy/).
Nothing in the CI3 application reads from it; it is there for reference when
comparing behaviour.

## Requirements

| Component | Version used |
| --------- | ------------ |
| PHP       | 8.1 or newer, with `mysqli` and `mbstring` |
| MySQL / MariaDB | MySQL 5.7+ / MariaDB 10.3+ |
| Web server | Apache with `mod_rewrite`, or any server that can route unknown paths to `index.php` |

CodeIgniter 3.1.13 is vendored in `system/`; there is no Composer install step.

## Install

1. Create the database and import the schema and demo data:

   ```bash
   mysql -u root -p -e "CREATE DATABASE db_voting_system"
   mysql -u root -p db_voting_system < sql/voting_system.sql
   ```

   `sql/voting_system.sql` builds the whole schema, so it can also be used to
   reset a scratch database. It ends with `SET FOREIGN_KEY_CHECKS` back to `1`,
   so the order of the `TRUNCATE` statements is safe.

   If you would rather create the schema yourself and skip the demo rows,
   migrate an empty database instead:

   ```bash
   php index.php migrate
   ```

2. Point `application/config/database.php` at it. The `default` group needs
   `hostname`, `username`, `password` and `database`; the defaults in the file
   are the local XAMPP defaults.

3. Set the base URL in `application/config/config.php`. It assumes the project
   lives in a folder called `cu-sbo-voting-system`:

   ```php
   $config['base_url'] = 'http://localhost/cu-sbo-voting-system/';
   $config['index_page'] = '';
   ```

   If you deploy it somewhere else, change `base_url` and the `RewriteBase` in
   `.htaccess` to match.

4. Make the writable CI3 directories writable by the web server:

   ```bash
   chmod -R 775 application/cache application/logs
   ```

5. Sign in and change the admin password. See **Accounts** below.

## Accounts

The seed creates three roles. Passwords are stored as plain text, matching the
original application; this is demo data only and is not suitable for production.

| Role | Email | Password |
| ---- | ----- | -------- |
| Admin | `admin@example.com` | `admin` |
| Moderator | `moderator@university.edu.ph` | `mod123` |
| Student | `juan.delacruz@student.edu.ph` and 16 others | `pass123` |

Every seeded student belongs to the College of Computer Studies, so the
department rules are observable straight away. Each student is flagged
`is_new`, which forces a password change on first sign-in.

## Roles

Access is enforced in the controllers, not in the views, so a hidden button is
never the only thing standing between a visitor and a delete.

| | Admin | Moderator | Student |
| --- | --- | --- | --- |
| Staff dashboard, users, elections, candidates, reports | yes | read only | no |
| Create / edit / delete | yes | no | no |
| Student ballot and receipt | no | no | yes |

A student may vote in an election for their own department, in any University
Wide election, and in nothing else. A voter can reach only their own ballot and
receipt.

## Layout

```
application/
  config/     autoload, database, form rules, hooks, migration, routes
  controllers/
  helpers/    schema_helper.php, for the constraints DB Forge cannot express
  hooks/      db_timezone.php
  language/   English overrides for missing validation messages
  migrations/ 001 to 005, in order
  models/     Query Builder only
  views/
assets/       css/ and img/
legacy/       the original procedural application
sql/          voting_system.sql, schema plus demo data
system/       CodeIgniter 3.1.13
```

## Migrations

Migrations run from the command line only. `Migrate` refuses HTTP requests, so
the endpoint cannot be used to reshape a live database.

```bash
php index.php migrate            # migrate to the configured version
php index.php migrate current    # report where you are
php index.php migrate status     # version on disk and configured target
php index.php migrate to 003     # migrate to a specific version
```

The runner lives in `application/controllers/Migrate.php`; the target version
and table name are in `application/config/migration.php`.

## Conversion notes and gotchas

Why the CI3 version is built the way it is. Each item is a decision that is not
obvious from reading the code, and most of them are places where CodeIgniter 3
behaves differently from what you would expect.

### Schema

**Migrations instead of a hand-maintained schema.** The original created its
tables with inline `CREATE TABLE` guarded by a "does this table exist" check.
`Migrate` is CLI-only and refuses HTTP requests, so the endpoint cannot be used
to reshape a live database.

**Query Builder and DB Forge instead of interpolated SQL.** Every model builds
queries through the query builder. CI3's DB Forge cannot express composite
unique indexes or foreign keys, so `application/helpers/schema_helper.php` adds
those in the migrations with escaped `ALTER TABLE`. The helpers are confined to
migrations; no model issues raw SQL.

**One submission per voter per election** is enforced by a unique key on
`voter_submissions (election_id, voter_id)`, on top of the check the ballot
route makes before rendering. `votes` carries a matching unique key on
`(election_id, voter_id, position)`.

**Votes survive a withdrawn candidate.** `votes.candidate_id` is
`ON DELETE SET NULL`, and the receipt renders such a row as withdrawn rather
than blank.

### Time

**A time reference that matches the data.** Election status is decided by
comparing `NOW()` with `open_at` and `close_at`, which hold naive wall-clock
values in Manila time. Three separate settings have to agree:

- `application/hooks/db_timezone.php` pins the MySQL session to `+08:00`, which
  is what makes the `NOW()` side of the comparison correct.
- `index.php` calls `date_default_timezone_set('Asia/Manila')`.
- `config.php`'s `time_reference` is `Asia/Manila`, but note that in CI3 it only
  changes what `now()` returns. It does **not** touch PHP's timezone, which is
  why `index.php` has to set it explicitly. Leaving that out makes an open
  election report as `upcoming`.

### Validation

**Validation that actually runs.** The original `view_users_viewmodel.php`
wrote posted values straight into its `INSERT` and `UPDATE`. The rules in
`application/config/form_rules.php` are enforced by `UserController::save()`,
`ElectionController::save()` and `CandidateController::save()`.

**`is_unique` cannot exclude the row being edited.** CI3's `is_unique($str,
$field)` takes only `table.field`; it splits the argument with
`sscanf('%[^.].%[^.]')` and has no way to say "unless it is this row". The
three-part form that looks like it should work,

```php
is_unique[users.email,id,{user_id}]
```

is not supported, and it does not fail safely: `sscanf` leaves the whole
remainder as the column name, so the generated query is

```sql
WHERE `email,id,{user_id}` = 'someone@example.com'
```

which is a database error rather than a validation message, so saving any user
returned a 500. `UserController::_unique_email()` asks the model the same
question properly.

**`callback[...]` is CI4 syntax.** Core only recognises the `callback_` prefix.
An unprefixed rule falls through to the parameter regex, which splits
`callback[_unique_email]` into the rule `callback` with the parameter
`_unique_email`, so `method_exists()` looks for a method literally named
`callback`, fails, and every save reports `Unable to access an error message ...
(callback)`. All callbacks here use the `callback_` prefix form.

**`is_natural_no` rejects `0`, and CI3 3.1.13 ships no message for it.** The
add forms post a hidden id of `0` to mean "create", which `is_natural_no` treats
as invalid, so creating any account always failed validation on that field. The
hidden id fields use `is_natural` instead. Any remaining use of `is_natural_no`
gets a real message from `application/language/english/form_validation_lang.php`,
because an app-level language file is searched before the system copy and adding
a key there does not mean editing `system/`.

**The password field has two contracts.** Adding an account requires at least 6
characters; editing one treats blank as "keep the stored password". A plain
`trim|min_length[6]` cannot express that, because `min_length` returns false for
an empty string and the "leave it blank to keep the current password"
instruction on the form becomes impossible to follow.
`UserController::_password_rule()` resolves it.

### Voting

**Off-ballot candidate ids are rejected, not silently stored.** A crafted id
from another election is discarded and the position is recorded as an
abstention, so the ballot can only ever record candidates on its own slate.

**Seed candidate ids are global `AUTO_INCREMENT`, not per election.** Election 1
holds ids 1–12, election 2 holds 13–15 and election 3 holds 16–18. Anything that
picks a candidate id should read it from the rendered ballot rather than
assuming the nth candidate of an election is id `n`.

**`csrf_regenerate` is off** so that a token stays valid across the ballot
submission, which redirects to the receipt.

**`is_online` and `session_id` mean a second sign-in for the same account
supersedes the first session.** Tests that sign the same user in twice need
distinct accounts.

### Routes and views

**The reports election selector navigates with JavaScript.** The selector is
built from all elections, but the report itself is per election, so the two do
not share a form target. The view reads the chosen value and navigates to
`reports/election/:id`.
