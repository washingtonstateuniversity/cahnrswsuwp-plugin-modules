<?php namespace WSUWP\CAHNRSWSUWP_Plugin_Modules;

?><style>
#core-module-options {
	padding-top: 20px;
}
#core-module-options h2 {
	border-bottom: 1px solid #ccc;
	padding-bottom: 6px;
	text-transform: uppercase;
	color: #0073aa;
}
.ccore-module-option-wrapper {
	display: inline-block;
	width: 175px;
	margin-right: 12px;
	margin-bottom: 12px;
	position: relative;
	box-sizing: border-box;
	vertical-align: top;
}
.ccore-module-option-wrapper label {
	background-color: #f1f1f1;
	display: block;
	-webkit-box-shadow: 0px 0px 5px 0px rgba(0,0,0,0.4);
	-moz-box-shadow: 0px 0px 5px 0px rgba(0,0,0,0.4);
	box-shadow: 0px 0px 5px 0px rgba(0,0,0,0.4);
	padding: 8px;
	box-sizing: border-box;
}
.ccore-module-option-wrapper input {
	position: absolute;
	top: 18px;
	left: 16px;
}
.ccore-module-option-wrapper .core-module-icon {
	display: block;
	height: 140px;
	background-color: #fff;
	background-size: contain;
	background-position: center;
	background-repeat: no-repeat;
}
.ccore-module-option-wrapper .core-module-title {
	display: block;
	padding: 12px;
	font-size: 18px;
	color: #981e32;
	font-weight: bold;
}
.ccore-module-option-wrapper .core-module-helper {
	display: block;
	padding: 0px 12px 12px;
	font-size: 13px;
}
.ccore-module-option-wrapper input:checked+label {
	background-color: #0073aa;
}
.ccore-module-option-wrapper input:checked+label .core-module-helper,
.ccore-module-option-wrapper input:checked+label .core-module-title {
	color: #fff;
}
#ccore-module-options-form .core-module-control {
	padding-top: 20px;
}
#ccore-module-options-form .core-module-control button {
	background-color: #0073aa;
	color: #fff;
	padding: 12px 18px;
	border: none;
	font-size: 16px;
	border-radius: 4px;
	cursor: pointer;
}
</style>
<div id="core-module-options">
	<h2>Available Modules</h2>
	<form id="ccore-module-options-form" method="post" >
		<div class="core-module-mesg">
		</div>
		<div class="core-module-options-set">
			<?php foreach ( $modules as $slug => $module ) : ?>
				<?php include 'module-option.php'; ?>
			<?php endforeach; ?>
		</div>
		<p class="submit">
			<input name="submit" id="submit" class="button button-primary" value="Save Changes" type="submit">
		</p>
	</form>
</div>
