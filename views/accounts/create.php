<?php if ($validation->error()): ?>
	<div class="alert alert-error">
		<?php echo implode('</p><p>', e((array) $validation->error())); ?>
	</div>
<?php endif ?>

<?php echo Form::open(); ?>
	<?php echo Form::hidden(\Config::get('security.csrf_token_key'), \Security::fetch_token()); ?>
	<div class="control-group <?php echo ($validation->error('first_name')) ? 'error' : ''; ?>">
		<div class="controls">
			<input class="span12" type="text" name="first_name" value="<?php echo $validation->validated('first_name'); ?>" id="inputFirstName" placeholder="FirstName">
		</div>
	</div>
	<div class="control-group <?php echo ($validation->error('last_name')) ? 'error' : ''; ?>">
		<div class="controls">
			<input class="span12" type="text" name="last_name" value="<?php echo $validation->validated('last_name'); ?>" id="inputLastName" placeholder="LastName">
		</div>
	</div>
	<div class="control-group <?php echo ($validation->error('email')) ? 'error' : ''; ?>">
		<div class="controls">
			<input class="span12" type="text" name="email" value="<?php echo $validation->validated('email'); ?>" id="inputEmail" placeholder="Email">
		</div>
	</div>
	<div class="control-group <?php echo ($validation->error('username')) ? 'error' : ''; ?>">
		<div class="controls">
			<input class="span12" type="text" name="username" value="<?php echo $validation->validated('username'); ?>" id="inputUsername" placeholder="Username">
		</div>
	</div>
	<div class="control-group">
		<div class="controls">
			<select name="group" class="span12">
				<?php foreach ($groupnames as $key => $value): ?>
				<option value="<?php echo $key; ?>" <?php echo ($key == $validation->validated('group')) ? 'selected' : ''; ?>><?php echo $value['name']; ?></option>	
				<?php endforeach ?>
			
			</select>
		</div>
	</div>
	<div class="control-group <?php echo ($validation->error('password')) ? 'error' : ''; ?>">
		<div class="controls">
			<input class="span12" type="password" name="password" id="inputPassword" placeholder="Password">
		</div>
	</div>
	<div class="control-group <?php echo ($validation->error('confirm_password')) ? 'error' : ''; ?>">
		<div class="controls">
			<input class="span12" type="password" name="confirm_password" id="inputConfirmPassword" placeholder="Confirm password">
		</div>
	</div>
	<button class="btn btn-large btn-primary" type="submit">Crate</button>
</form>