<?php

require_once __DIR__ . '/../src/config/session_config.php';
require_once __DIR__ . '/../src/config/access_control.php';
require_once __DIR__ . '/../src/config/db_config.php';
require_once __DIR__ . '/../src/config/config.php';


$successMessage = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : null;
unset($_SESSION['success_message']);

$verificationMessage = isset($_SESSION['verification_message']) ? $_SESSION['verification_message'] : null;
$modalTitle = isset($_SESSION['modal_title']) ? $_SESSION['modal_title'] : 'Confirm Action';
$confirmButtonText = isset($_SESSION['confirm_button_text']) ? $_SESSION['confirm_button_text'] : 'Confirm';
?>


<!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <!-- <h5 class="modal-title" id="successModalLabel"></h5> -->
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                <?php if ($successMessage): ?>
                    <p><?php echo htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8'); ?></p>
                <?php endif; ?></div>
            </div>
        </div>
    </div>


<!-- Verification Modal -->
<div class="modal fade" id="verificationModal" tabindex="-1" role="dialog" aria-labelledby="verificationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="verificationModalLabel">Confirm Action</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="verificationMessage">Are you sure you want to proceed?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmActionButton">Confirm</button>
            </div>
        </div>
    </div>
</div>


<!-- Discard Changes Modal -->
<div class="modal fade" id="discardChangesModal" tabindex="-1" role="dialog" aria-labelledby="discardChangesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="discardChangesModalLabel">Discard Changes</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to discard changes?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDiscardButton">Discard Changes</button>
            </div>
        </div>
    </div>
</div>

 