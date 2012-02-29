function refreshConcept() {
    var navigationUri = $("#navigation-tree").data("selectedUri");
    var authorityUri = $("#authority").val();

    if (!navigationUri) {
        return;
    }

    $("#concept-content").empty().loadingSign("show");

    $.get("services/conceptSection.php",
        { 
            navigationUri: navigationUri,
            authorityUri: authorityUri 
        },
        function (html) {
            $("#concept-content").loadingSign("hide").html(html);
        }
    );
}


function handleTreeClick(event, data) {
    $("#navigation-tree .selected").removeClass("selected");
    $(this).addClass("selected");

    var label = $.trim($(this).text());
    var uri = $(this).attr("href").substring(1); // remove the #
    $("#navigation-tree").data("selectedUri", uri);

    refreshConcept();

    event.preventDefault();
}


function handleAuthorityChange(event, data) {
    var $tree = $("#navigation-tree");
    markServiceLinks($tree);
    refreshConcept();
}


function treeLoaded(event, data) {
    $tree = $(this);
    $tree.find("a").mouseenter(function () {
        var $a = $(this);
        if ($a.hasClass("has-navigation-links") && $a.hasClass("has-service-links")) {
            $a.attr("title", "General and local links");
        }
        else if ($a.hasClass("has-navigation-links")) {
            $a.attr("title", "General links");
        }
        else if ($a.hasClass("has-service-links")) {
            $a.attr("title", "Local links");
        }
    });

    $.getJSON("services/urisWithLinks.php", { x: "as" }, function (data) {
        var uris = data.uris;
        var uri;

        for (var i = 0; i < uris.length; i++) {
            uri = uris[i];

            $tree.find("a[href='#" + uri + "']").addClass("has-navigation-links");
        }

        markServiceLinks($tree, function () {
            $tree.loadingSign("hide").slideDown();
        });
    });
}


function prepareTreeLegend() {
    var $div = $("#navigation-tree-legend");
    var $button = $("#navigation-tree-legend-button");
    var $content = $("#navigation-tree-legend-content");

    $button.click(function (event) {
        $content.slideDown("fast", function () {
            $content.data("isVisible", true);
        });
        event.preventDefault();
    });

    $(document).click(function (event) {
        if ($content.data("isVisible")) {
            $content.hide();
            $content.data("isVisible", false)
        }
    });
}


function markServiceLinks($tree, loaded) {
    var authorityUri = $("#authority").val();
    if (authorityUri === "") {
        $tree.find("a.has-service-links").removeClass("has-service-links"); 
        if (loaded) {
            loaded();
        }
        return;
    }
    $.getJSON("services/urisWithLinks.php", { type: "service-links", authorityUri: authorityUri }, function (data) {
        var uris = data.uris;
        var uri;
        $tree.find("a.has-service-links").removeClass("has-service-links");

        for (var i = 0; i < uris.length; i++) {
            uri = uris[i];

            $tree.find("a[href='#" + uri + "']").addClass("has-service-links");
        }

        if (loaded) {
            loaded();
        }
    });
}


$(function () {
    $("#authority").selectAutoComplete({
        "unselected-label": "start typing council name",
        select: handleAuthorityChange
    });

    $("#navigation-tree")
        .loadingSign()
        .loadingSign("show")
        .bind("loaded.jstree", treeLoaded)
        .jstree({
            "core": {
                "animation": "fast"
            },
            "themes": {
                "theme": "default",
                "dots": false,
                "icons": false
            },
            "plugins": ["themes", "html_data"]
        })
	    .delegate("a", "click", handleTreeClick);

    $("#concept-content").loadingSign();

    prepareTreeLegend();
});