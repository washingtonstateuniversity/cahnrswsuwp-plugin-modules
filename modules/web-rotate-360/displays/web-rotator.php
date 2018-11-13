<?php namespace WSUWP\CAHNRSWSUWP_Plugin_Modules;

?>
<div id="web-rotator-content" style="height:<?php echo esc_attr( $height ); ?>;width:<?php echo esc_attr( $width ); ?>">
	<div id="wr360PlayerId" class="wr360_player" style="background-color:#FFFFFF;"></div>
	<script language="javascript" type="text/javascript">
		var rotator = WR360.ImageRotator.Create('wr360PlayerId');
		rotator.licenseFileURL = '<?php echo ccore_get_plugin_url(); ?>modules/web-rotate-360/vendor/license.lic';
		rotator.settings.configFileURL = '<?php echo esc_url( $xml ); ?>';
		rotator.settings.graphicsPath = '<?php echo ccore_get_plugin_url(); ?>modules/web-rotate-360/vendor/imagerotator/html/img/thin';
		rotator.settings.googleEventTracking = false;
		rotator.settings.responsiveBaseWidth = 0;
		rotator.settings.responsiveMinHeight = 0;
		rotator.runImageRotator();
	</script>
</div>
