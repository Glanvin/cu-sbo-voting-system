<!-- ── Add User Modal ────────────────────────────────────────────── -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #48672F;">
                <h5 class="modal-title">Add New User</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body p-4">

                    <div class="row g-3 mb-3">
                        <div class="col-12 col-sm-6">
                            <label class="form-label">First Name</label>
                            <input type="text" name="firstname" class="form-control" placeholder="e.g., Juan" required>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label">Last Name</label>
                            <input type="text" name="lastname" class="form-control" placeholder="e.g., Dela Cruz" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">School ID</label>
                        <input type="text" name="school_id" class="form-control" placeholder="e.g., 2024-0001" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-control" placeholder="email@cu.edu.ph" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Department / College</label>
                        <select name="department" class="form-select" required>
                            <option value="" disabled selected>Select a college</option>
                            <option value="CCS">College of Computer Studies (CCS)</option>
                            <option value="COE">College of Engineering (COE)</option>
                            <option value="CBA">College of Business Administration (CBA)</option>
                            <option value="CAS">College of Arts and Sciences (CAS)</option>
                            <option value="CED">College of Education (CED)</option>
                            <option value="CRIM">College of Criminology (CRIM)</option>
                            <option value="COHS">College of Health and Sciences (COHS)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Temporary Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">System Role</label>
                        <select name="role" class="form-select" required>
                            <option value="student">Student / Voter</option>
                            <option value="moderator">Moderator</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>

                </div>
                <div class="modal-footer bg-light border-0 rounded-bottom">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="add_user" class="btn text-white" style="background-color: #48672F;">Save User</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- ── Edit User Modal ───────────────────────────────────────────── -->
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white border-0">
                <h5 class="modal-title">Edit User</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body p-4">
                    <input type="hidden" name="user_id" id="edit_id">

                    <div class="row g-3 mb-3">
                        <div class="col-12 col-sm-6">
                            <label class="form-label">First Name</label>
                            <input type="text" name="firstname" id="edit_firstname" class="form-control" required>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label">Last Name</label>
                            <input type="text" name="lastname" id="edit_lastname" class="form-control" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">School ID</label>
                        <input type="text" name="school_id" id="edit_school_id" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" id="edit_email" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Department / College</label>
                        <select name="department" id="edit_department" class="form-select" required>
                            <option value="" disabled>Select a college</option>
                            <option value="CCS">College of Computer Studies (CCS)</option>
                            <option value="COE">College of Engineering (COE)</option>
                            <option value="CBA">College of Business Administration (CBA)</option>
                            <option value="CAS">College of Arts and Sciences (CAS)</option>
                            <option value="CED">College of Education (CED)</option>
                            <option value="CRIM">College of Criminology (CRIM)</option>
                            <option value="COHS">College of Health and Sciences (COHS)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">New Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Leave blank to keep current password">
                        <div class="form-text text-muted small">Only fill this out if you need to reset the user's password.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">System Role</label>
                        <select name="role" id="edit_role" class="form-select" required>
                            <option value="student">Student / Voter</option>
                            <option value="moderator">Moderator</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>

                </div>
                <div class="modal-footer bg-light border-0 rounded-bottom">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="edit_user" class="btn btn-primary">Update User</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- ── Delete User Modal ─────────────────────────────────────────── -->
<div class="modal fade" id="deleteUserModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-body text-center pt-4 pb-3">
                    <span class="material-symbols-outlined text-danger mb-2" style="font-size: 3rem;">warning</span>
                    <h5 class="fw-bold mb-3">Delete User?</h5>
                    <p class="text-muted small">
                        Are you sure you want to delete <strong id="delete_email_display"></strong>?
                        This action cannot be undone.
                    </p>
                    <input type="hidden" name="user_id"     id="delete_id">
                    <input type="hidden" name="delete_email" id="delete_email_input">
                </div>
                <div class="modal-footer border-0 d-flex justify-content-center bg-light rounded-bottom">
                    <button type="button" class="btn btn-outline-secondary px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="delete_user" class="btn btn-danger px-4">Yes, Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function () {

        // ── Edit Modal — populate fields from the clicked row's data attributes ──
        const editModal = document.getElementById('editUserModal');
        if (editModal) {
            editModal.addEventListener('show.bs.modal', (e) => {
                const btn = e.relatedTarget;

                document.getElementById('edit_id').value         = btn.getAttribute('data-id');
                document.getElementById('edit_school_id').value  = btn.getAttribute('data-school-id');
                document.getElementById('edit_firstname').value  = btn.getAttribute('data-firstname');
                document.getElementById('edit_lastname').value   = btn.getAttribute('data-lastname');
                document.getElementById('edit_email').value      = btn.getAttribute('data-email');
                document.getElementById('edit_role').value       = btn.getAttribute('data-role');
                document.getElementById('edit_department').value = btn.getAttribute('data-department');
            });
        }

        // ── Delete Modal — populate the hidden ID and display the email ─────────
        const deleteModal = document.getElementById('deleteUserModal');
        if (deleteModal) {
            deleteModal.addEventListener('show.bs.modal', (e) => {
                const btn = e.relatedTarget;

                document.getElementById('delete_id').value           = btn.getAttribute('data-id');
                document.getElementById('delete_email_input').value  = btn.getAttribute('data-email');
                document.getElementById('delete_email_display').textContent = btn.getAttribute('data-email');
            });
        }
    });
</script>