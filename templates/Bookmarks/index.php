<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Bookmark> $bookmarks
 */
?>
<div class="bookmarks index content">
    <?= $this->Html->link(__('New Bookmark'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Bookmarks') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= __('ID') ?></th>
                    <th><?= __('User') ?></th>
                    <th><?= __('Title') ?></th>
                    <th><?= __('Created') ?></th>
                    <th><?= __('Modified') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bookmarks as $bookmark): ?>
                <tr>
                    <td><?= $this->Number->format($bookmark->id) ?></td>
                    <td><?= $bookmark->hasValue('user') ? $this->Html->link($bookmark->user->email, ['controller' => 'Users', 'action' => 'view', $bookmark->user->id]) : '' ?></td>
                    <td><?= h($bookmark->title) ?></td>
                    <td><?= h($bookmark->created) ?></td>
                    <td><?= h($bookmark->modified) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $bookmark->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $bookmark->id]) ?>
                        <?= $this->Form->postLink(
                            __('Delete'),
                            ['action' => 'delete', $bookmark->id],
                            [
                                'method' => 'delete',
                                'confirm' => __('Are you sure you want to delete # {0}?', $bookmark->id),
                            ]
                        ) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
