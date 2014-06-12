function normalSync() {
    jvideo.jQuery.get('index.php', {
        option: "com_jvideo",
        view: "jvideo",
        task: "normalSync",
        format: "raw"
    });
}

function consoleSync() {
    jvideo.jQuery.get('index.php', {
        option: "com_jvideo",
        view: "jvideo",
        task: "consoleSync",
        format: "raw"
    });
}

function manualSync() {
    jvideo.jQuery('#manualSyncDiv').load('index.php', {
        option: "com_jvideo",
        view: "jvideo",
        task: "consoleSync",
        format: "raw",
        manual: "1"
    });
}