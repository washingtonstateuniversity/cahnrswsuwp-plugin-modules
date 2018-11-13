<?php namespace WSUWP\CAHNRSWSUWP_Plugin_Modules;

?><div class="ccore-module-option-wrapper">
	<input id="core-module-<?php echo $slug; ?>" type="checkbox" name="_core_modules[]" value="module-<?php echo $slug; ?>" />
	<label for="core-module-<?php echo $slug; ?>">
		<span class="core-module-icon" style="background-image: url(<?php echo $module['icon']; ?>);"></span>
		<span class="core-module-title"><?php echo $module['label']; ?></span>
		<span class="core-module-helper"><?php echo $module['helper_text']; ?></span> 
	</label>
</div>
