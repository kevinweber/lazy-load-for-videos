<div id="mc_embed_signup">
<form action="<?= LL_NEWS_ACTION_URL; ?>" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
	<div class="mc-field-group updated">
		<label for="mce-EMAIL"><?= LL_NEWS_TEXT; ?></label><br>
		<input type="email" value="Enter your email address" name="EMAIL" class="required email" id="mce-EMAIL" onclick="this.focus();this.select()" onfocus="if(this.value == '') { this.value = this.defaultValue; }" onblur="if(this.value == '') { this.value = this.defaultValue; }">
		<input type="hidden" name="GROUPS" id="GROUPS" value="<?= LL_NEWS_GROUP; ?>" />
		<input type="submit" value="<?= LL_NEWS_BUTTON; ?>" name="subscribe" id="mc-embedded-subscribe" class="button">
	</div>
	<div id="mce-responses" class="clear">
		<div class="response" id="mce-error-response" style="display:none"></div>
		<div class="response" id="mce-success-response" style="display:none"></div>
	</div>
    <div style="position: absolute; left: -5000px;"><input type="text" name="<?= LL_NEWS_NAME; ?>" tabindex="-1" value=""></div>
</form>
</div>