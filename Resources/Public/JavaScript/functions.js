$(document).ready(function() {
    if(!$('#tagCloud').tagcanvas({
      textColour: $("input#tagcloud_textcolor").val(),
      outlineColour: $("input#tagcloud_bordercolor").val(),
      reverse: true,
      depth: 0.8,
      maxSpeed: 0.05
    },'tags')) {
      $('#tagcloudContainer').hide();
    }
  });