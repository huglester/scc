<div class="row">
	<div class="span12">
		<?php echo Form::open(array('class' => 'form-search pull-right')); ?>
			<input class="input-medium search-query" type="text" name="search" value="<?php echo $current_search; ?>" id="inputTitle" placeholder="Title">
			<button class="btn btn-small btn-success" type="submit"><i class="icon-search"></i> Search</button>
		</form>
		<?php echo Html::anchor('hosts/create', '<i class="icon-book"></i> Create', array('class' => 'btn btn-small btn-success')); ?>
	</div>

	<div class="span12 rawr">
		<?php if ($hosts): ?>
			<table class="table table-bordered table-striped table-hover">
				<thead>
					<tr>
						<th>Title</th>
						<th>Link</th>
						<th>FTP/SSH Password</th>
						<th>Database</th>
						<th>Database Password</th>
						<th>Created by</th>
						<th>Created at</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
		<?php foreach ($hosts as $key => $value): ?>
					<tr>
						<td><?php echo $value->title; ?></td>
						<td><a target="\blank" href="<?php echo 'http://'.$value['title'].'.'.$server_name; ?>"><?php echo $value['title']; ?></a></td>
						<td><?php echo $value->password; ?></td>
					<?php if($value->db_password): ?>
						<td><a target="\blank" href="<?php echo 'http://'.$value['title'].'.'.$server_name.'/phpmyadmin?pma_username='.$value->title.'&pma_password='.$value->db_password; ?>">Database</a></td>
					<?php else: ?>
						<td>-</td>
					<?php endif; ?>
						<td><?php echo ($value->db_password) ? $value->db_password : '-'; ?></td>
						<td><?php echo $value['first_name'].' '.Str::truncate($value['last_name'], 1, '.'); ?></td>
						<td><?php echo Date::forge($value['created_at'])->format('%Y-%m-%d %H:%M:%S'); ?></td>
						<td>
							<?php echo Html::anchor('hosts/delete/'.$value['id'], '<i class="icon-trash"></i> Delete', array('class' => 'btn btn-small btn-danger')); ?>
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