<?php if ($validation->error()): ?>
	<div class="alert alert-error">
		<?php echo implode('</p><p>', e((array) $validation->error())); ?>
	</div>
<?php endif ?>

<?php echo Form::open(); ?>
	<?php echo Form::hidden(\Config::get('security.csrf_token_key'), \Security::fetch_token()); ?>
	<div class="control-group <?php echo ($validation->error('email')) ? 'error' : ''; ?>">
		<div class="controls">
			<input class="span12" type="text" name="email" value="<?php echo $validation->validated('email'); ?>" id="inputEmail" placeholder="Email">
		</div>
	</div>
	<div class="control-group <?php echo ($validation->error('password')) ? 'error' : ''; ?>">
		<div class="controls">
			<input class="span12" type="password" name="password" id="inputPassword" placeholder="Password">
		</div>
	</div>
	<button class="btn btn-large btn-primary" type="submit">Sign in</button>
</form>