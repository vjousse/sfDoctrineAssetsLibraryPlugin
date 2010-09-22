function setImageField(src) {
  try {
    var mediaLibrary = tinyMCEPopup.getWindowArg("mediaLibrary");
    mediaLibrary.fileBrowserReturn(src, tinyMCEPopup);
  } catch(e) {
    window.opener.tinyMCEPopup.fileBrowserReturn(src,null);
    window.close();
  }
}

function addAsset(assetId,widgetId,url,thumb) {
    parent.addAssetForm(assetId,widgetId,url,thumb);
}