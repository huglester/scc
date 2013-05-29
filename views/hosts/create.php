<?php if ($validation->error()): ?>
	<div class="alert alert-error">
		<?php echo implode('</p><p>', e((array) $validation->error())); ?>
	</div>
<?php endif ?>

<?php echo Form::open(); ?>
	<?php echo Form::hidden(\Config::get('security.csrf_token_key'), \Security::fetch_token()); ?>
	<div class="control-group <?php echo ($validation->error('title')) ? 'error' : ''; ?>">
		<div class="controls">
			<input class="span12" type="text" name="title" value="<?php echo $validation->validated('title'); ?>" id="inputTitle" placeholder="title">
		</div>
	</div>
	<button class="btn btn-large btn-primary" type="submit">Crate</button>
</form>