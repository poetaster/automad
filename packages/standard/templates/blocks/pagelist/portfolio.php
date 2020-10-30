<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<@ set { :hideThumbnails: @{ checkboxHideThumbnails } } @>
<div class="masonry<@ if @{ :pagelistDisplayCount } > 3 @> am-stretched<@ end @>">
	<@ foreach in pagelist ~@>
		<div class="masonry-item" <@ ../../snippets/colors_inline.php @>>
			<div class="masonry-content uk-panel uk-panel-box">
				<@ if not @{ :hideThumbnails } and not @{ pageIconSvg } @>
					<@~ ../../snippets/set_imageteaser_variable.php @>
					<@~ if @{ :imageTeaser } @>
						<div class="uk-panel-teaser">
							<a href="@{ url }"><img src="@{ :imageTeaser }"></a>
						</div>
					<@~ end ~@>
				<@ end @>
				<div class="uk-panel-title panel-body">
					<a href="@{ url }">
						<@ ../../snippets/icon.php @>
						@{ title }
					</a>
					<div class="text-subtitle">
						<@ ../../snippets/date.php @>
						<@ if @{ date } and @{ tags } @><br><@ end @>
						<@ ../../snippets/tags.php @>
					</div>
				</div>
				<@ ../../snippets/more.php @>
			</div>
		</div>
	<@ else @>
		<div class="masonry-item">
			<div class="masonry-content uk-panel uk-panel-box">
				<div class="uk-panel-title uk-margin-remove">
					@{ notificationNoSearchResults | def ('No Pages Found') }
				</div>
			</div>
		</div>
	<@~ end @>
</div>