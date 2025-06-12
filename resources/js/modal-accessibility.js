document.addEventListener('DOMContentLoaded', function () {
    // Store the last focused element before modal opens
    let lastFocusedElement = null;

    // Function to trap focus inside modal
    function trapFocus(modal) {
        const focusableElementsString = 'a[href], area[href], input:not([disabled]), select:not([disabled]), textarea:not([disabled]), button:not([disabled]), iframe, object, embed, [tabindex="0"], [contenteditable]';
        const focusableElements = modal.querySelectorAll(focusableElementsString);
        const firstFocusableElement = focusableElements[0];
        const lastFocusableElement = focusableElements[focusableElements.length - 1];

        // Remove any existing keydown listener to prevent stacking
        if (modal._keydownListener) {
            modal.removeEventListener('keydown', modal._keydownListener);
        }

        modal._keydownListener = function (e) {
            const isTabPressed = (e.key === 'Tab' || e.keyCode === 9);

            if (!isTabPressed) {
                return;
            }

            if (e.shiftKey) { // Shift + Tab
                if (document.activeElement === firstFocusableElement) {
                    e.preventDefault();
                    lastFocusableElement.focus();
                }
            } else { // Tab
                if (document.activeElement === lastFocusableElement) {
                    e.preventDefault();
                    firstFocusableElement.focus();
                }
            }
        };

        modal.addEventListener('keydown', modal._keydownListener);
    }

    // Listen for Bootstrap modal show event
    document.querySelectorAll('.modal').forEach(function (modal) {
        modal.addEventListener('show.bs.modal', function () {
            lastFocusedElement = document.activeElement;
            // Set focus to the modal itself or first focusable element
            const focusableElementsString = 'a[href], area[href], input:not([disabled]), select:not([disabled]), textarea:not([disabled]), button:not([disabled]), iframe, object, embed, [tabindex="0"], [contenteditable]';
            const focusableElements = modal.querySelectorAll(focusableElementsString);
            if (focusableElements.length) {
                focusableElements[0].focus();
            } else {
                modal.focus();
            }
            trapFocus(modal);
        });

        modal.addEventListener('hide.bs.modal', function () {
            // Remove keydown listener when modal is hiding
            if (modal._keydownListener) {
                modal.removeEventListener('keydown', modal._keydownListener);
                modal._keydownListener = null;
            }
        });

        modal.addEventListener('hidden.bs.modal', function () {
            // Return focus to the last focused element before modal opened
            if (lastFocusedElement) {
                lastFocusedElement.focus();
            }
        });
    });
});
