$(function () {
    $('[data-confirm]').on('click', function (event) {
        if (!confirm($(this).data('confirm'))) {
            event.preventDefault();
        }
    });

    $('.custom-file-input').on('change', function () {
        const fileName = this.files && this.files.length ? this.files[0].name : '';
        $(this).closest('.mb-3').find('.selected-file-name').text(fileName);
    });
});
