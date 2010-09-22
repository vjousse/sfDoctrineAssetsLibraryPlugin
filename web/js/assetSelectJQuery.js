//Appelé depuis le popup sfAsset

function basename(path) {
    return path.replace(/\\/g,'/').replace( /.*\//, '' );
}

function addAssetToTab(widgetId,assetId, url)
{
    if(eval('isMany'+widgetId)==0)
    {
        $('#assets_content_'+widgetId).empty();
        $('#assets_content_'+widgetId).html('<ul class="sortableJ"></ul>');
    }
    

    //for now, an asset is an Image ...
    var img = new Image();
    img.src = url;
    $('#assets_content_'+widgetId+' .sortableJ').append('<li id="item_' + assetId + '"></li>');
    //The image itself
    $('#assets_content_'+widgetId+' .sortableJ #item_'+assetId).append(img);
    //The button to remove the image
    $('#assets_content_'+widgetId+' .sortableJ #item_'+assetId).append('<br /><span id="del_'+widgetId+'_' + assetId + '" class="asset_del"><img src="/images/back/cross.png" alt="Enlever" /></span>');

    //It's a multiple choice widget
    if(eval('isMany'+widgetId) == 1)
    {
        $('#assets_content_'+widgetId+' .sortableJ #item_'+assetId).append('<input type="hidden" name="' + assetsFormName + '[]" value="' + assetId + '" />');
    }
    else
    {
        //Just a single choice widget, so no tab
        $('#assets_content_'+widgetId+' .sortableJ #item_'+assetId).append('<input type="hidden" name="' + assetsFormName + '" value="' + assetId + '" />');

    }

    refreshDelEvent(widgetId);
}

function removeAssetFromTab(widgetId,assetId)
{
    $('#item_'+assetId).remove();
    refreshDelEvent(widgetId);
}

function refreshDelEvent(widgetId) {
    //For each del cross, add the remove method
    $('.asset_del').each(function() {
        $(this).click(function() {
            var id = $(this).attr('id');
            var widgetIdStart = 'del_' + widgetId;
            var assetId = id.substring(widgetIdStart.length+1,id.length);
            removeAssetFromTab(widgetId,assetId);
        });

    });
}


function addAssetForm(assetId, widgetId, full, thumb)
{
    $.fn.colorbox.close();

    addAssetToTab(widgetId,assetId,thumb);
}

$(document).ready(function(){


    //Popup assets
    $(".iframe").colorbox({
        width:"80%",
        height:"80%",
        iframe:true,
        opacity: 0.3

    });

});