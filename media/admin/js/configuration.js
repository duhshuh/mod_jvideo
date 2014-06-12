var jvideo;

(function(window, document, $) {
    jvideo = jvideo || {};
    jvideo.configuration = jvideo.configuration || {};

    jvideo.configuration.setupTabs = function() {
        $(document).ready(function() {
            $("#configTabs").tabs();
            $('#settingsAccordion').accordion({header: 'h3.settingsHeader', collapsible: true, autoHeight: false});
            $('#permissionAccordion').accordion({header: 'h3.permissionHeader', collapsible: true, autoHeight: false});
            $('#mappingAccordion').accordion({header: 'h3.mappingHeader', collapsible: true, active: false, autoHeight: false});
        });
    };

    jvideo.configuration.initExtensionVersionCheck = function() {
        $(document).ready(function() {
            getNewestVersion();
        });
    };

    var getNewestVersion = function() {
        jvideo.jQuery.get(
            'index.php',
            {
                option: "com_jvideo",
                view: "configuration",
                task: "newestVersion",
                format: "raw"
            },
            function(result) {
                var com = document.getElementById('com_jvideo_version');

                if (com.innerHTML < result)
                    com.innerHTML = getOutdatedNoticeHtml(com.innerHTML, result);
                //else
                //    com.innerHTML = getUpToDateNoticeHtml(result);

                /*
                var mod = document.getElementById('mod_jvideo_version');
                var plg_search = document.getElementById('plg_jvideo_search_version');
                var plg_content = document.getElementById('plg_jvideo_content_version');

                if (mod && mod.innerHTML != result && mod.innerHTML != "Not Installed")
                    mod.innerHTML = getOutdatedNoticeHtml(mod.innerHTML, result);
                else if (mod && mod.innerHTML != "Not Installed")
                    mod.innerHTML = getUpToDateNoticeHtml(result);

                if (plg_search.innerHTML != result && plg_search.innerHTML != "Not Installed")
                    plg_search.innerHTML = getOutdatedNoticeHtml(plg_search.innerHTML, result);
                else if (plg_search.innerHTML != "Not Installed")
                    plg_search.innerHTML = getUpToDateNoticeHtml(result);

                if (plg_content.innerHTML != result && plg_content.innerHTML != "Not Installed")
                    plg_content.innerHTML = getOutdatedNoticeHtml(plg_content.innerHTML, result);
                else if (plg_content.innerHTML != "Not Installed")
                    plg_content.innerHTML = getUpToDateNoticeHtml(result);
                */
            });
    };

    function getOutdatedNoticeHtml(oldVersion, newVersion) {
        return "<span class=\"outdatedNotice\">"
             + "<span style=\"color: red;\">" + oldVersion + "</span>"
             + "&nbsp;&nbsp;&mdash;&nbsp;&nbsp;<span>" + newVersion + " now available</span> "
             + "[<a href=\"http://jvideo.warphd.com/download/\" target=\"_blank\">Download</a>]</span>";
    }

    function getUpToDateNoticeHtml(version) {
        return "<span style=\"color: green;\">" + version + "</span";
    }
})(window, document, jvideo.jQuery);
