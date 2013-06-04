<div class="row">
	<div class="span12">
		<?php echo Html::anchor('accounts/create', '<i class="icon-book"></i> Create', array('class' => 'btn btn-small btn-success pull-right')); ?>
	</div>

	<div class="span12 rawr">
		<?php if ($accounts): ?>
			<table class="table table-bordered table-striped table-hover">
				<thead>
					<tr>
						<th>First name</th>
						<th>Last name</th>
						<th>Email</th>
						<th>Group</th>
						<th>Last login</th>
						<th>Updated at</th>
						<th>Created at</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
		<?php foreach ($accounts as $key => $value): ?>
					<tr>
						<td><?php echo $value['first_name']; ?></td>
						<td><?php echo $value['last_name']; ?></td>
						<td><?php echo $value['email']; ?></td>
						<td><?php echo $groupnames[$value['group']]['name']; ?></td>
						<td><?php echo $value['last_login'] ? Date::forge($value['last_login'])->format('%Y-%m-%d %H:%M:%S') : '-'; ?></td>
						<td><?php echo $value['updated_at'] ? Date::forge($value['updated_at'])->format('%Y-%m-%d %H:%M:%S') : '-'; ?></td>
						<td><?php echo Date::forge($value['created_at'])->format('%Y-%m-%d %H:%M'); ?></td>
						<td>
							<?php echo Html::anchor('accounts/edit/'.$value['id'], '<i class="icon-pencil"></i> Edit', array('class' => 'btn btn-small btn-info')); ?>
							<?php echo Html::anchor('accounts/delete/'.$value['id'], '<i class="icon-trash"></i> Delete', array('class' => 'btn btn-small btn-danger')); ?>
						</td>
					</tr>
		<?php endforeach ?>
				</tbody>
			</table>
		<?php else: ?>
			<div class="alert alert-error">
				Sorry we cannot find any records
			</div>
		<?php endif ?>
	</div>
</div>