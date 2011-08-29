    function showSuccessToast(message) {
        $().toastmessage('showSuccessToast', message);
    }
    function showStickySuccessToast(message) {
        $().toastmessage('showToast', {
            text     : message,
            sticky   : true,
            position : 'top-right',
            type     : 'success',
            closeText: '',
            close    : function () {
                console.log("toast is closed ...");
            }
        });

    }
    function showNoticeToast(message) {
        $().toastmessage('showNoticeToast', message);
    }
    function showStickyNoticeToast(message) {
        $().toastmessage('showToast', {
             text     : message,
             sticky   : true,
             position : 'top-right',
             type     : 'notice',
             closeText: '',
             close    : function () {console.log("toast is closed ...");}
        });
    }
    function showWarningToast(message) {
        $().toastmessage('showWarningToast', message);
    }
    function showStickyWarningToast(message) {
        $().toastmessage('showToast', {
            text     : message,
            sticky   : true,
            position : 'top-right',
            type     : 'warning',
            closeText: '',
            close    : function () {
                console.log("toast is closed ...");
            }
        });
    }
    function showErrorToast(message) {
        $().toastmessage('showErrorToast', message);
    }
    function showStickyErrorToast(message) {
        $().toastmessage('showToast', {
            text     : message,
            sticky   : true,
            position : 'top-right',
            type     : 'error',
            closeText: '',
            close    : function () {
                console.log("toast is closed ...");
            }
        });
    }
