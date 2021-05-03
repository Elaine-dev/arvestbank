# Media Video Micromodal

## Creates a modal popup for remote videos.

The module uses the micromodal.js library to generate modal popup for remote videos from the media module.  Specifically works with core media oembed videos, that have the "field_media_oembed_video" field for the remote video URL.

Lightweight and non-dependant on jQuery.

Steps for Setup:
- Enable the module.
- Manage the display settings for the "video" media bundle.

To display the video thumbnail linking to the modal:
- Display the "Thumbnail" field.
- Set the "Format" to "Micromodal field formatter".
- Use the settings to select the image style for the thumbnail.

To display the media name linking to the modal:
- Display the "Name" field.
- Set the "Format" to "Micromodal field formatter".
- Use the settings to add additional styles to a &lt;span&gt; tag around the name.
