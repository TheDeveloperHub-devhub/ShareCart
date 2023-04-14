require([
    'jquery',
    'ko',
    'Magento_Ui/js/modal/modal'
], function ($, ko, modal) {

    let options = {
        type: 'popup',
        responsive: true,
        innerScroll: true,
        title: '',
        buttons: []
    }

    $(document).ready(function () {
        $(".popupBtn").click(function () {
            let modalId = $(this).attr('data-modal-id');
            let popup = modal(options, $('#' + modalId));
            $('#' + modalId).modal('openModal');
        })
    })
})






