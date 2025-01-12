/**
 * Custom scripts
 */

document.addEventListener('DOMContentLoaded', function() {
    const textareas = document.querySelectorAll('.wp-code-mirror');
    textareas.forEach(function(textarea) {
        wp.CodeMirror.fromTextArea(textarea, {
            lineNumbers: true,
            mode: 'htmlmixed',
            theme: 'tomorrow-night-eighties',
            lineWrapping: true
        });
    });
});
