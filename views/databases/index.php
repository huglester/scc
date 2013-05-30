<div class="row">
	<div class="span12">
		<?php echo Form::open(array('class' => 'form-search pull-right')); ?>
			<input class="input-medium search-query" type="text" name="search" value="<?php echo $current_search; ?>" id="inputTitle" placeholder="Title">
			<button class="btn btn-small btn-success" type="submit"><i class="icon-search"></i> Search</button>
		</form>
		<?php echo Html::anchor('databases/create', '<i class="icon-book"></i> Create', array('class' => 'btn btn-small btn-success')); ?>
	</div>

	<div class="span12 rawr">
		<?php if ($databases): ?>
			<table class="table table-bordered table-striped table-hover">
				<thead>
					<tr>
						<th>Title</th>
						<th>Password</th>
						<th>Database</th>
						<th>Created by</th>
						<th>Created at</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
		<?php foreach ($databases as $key => $value): ?>
					<tr>
						<td><?php echo $value->title; ?></td>
						<td><?php echo $value->password; ?></td>
						<td><a target="\blank" href="<?php echo 'http://'.$value['title'].'.'.$server_name.'/phpmyadmin?pma_username='.$value->title.'&pma_password='.$value->password; ?>">Database</a></td>
						<td><?php echo $value['first_name'].' '.$value['last_name']; ?></td>
						<td><?php echo Date::forge($value['created_at'])->format('%Y-%m-%d'); ?></td>
						<td>
							<?php echo Html::anchor('databases/delete/'.$value['id'], '<i class="icon-trash"></i> Delete', array('class' => 'btn btn-small btn-danger')); ?>
						</td>
					</tr>
		<?php endforeach ?>
				</tbody>
			</table>
			<?php echo $pagination->render(); ?>
		<?php else: ?>
			<div class="alert alert-error">
				Sorry we cannot find any records
			</div>
		<?php endif ?>
	</div>
</div>