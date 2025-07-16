<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Bookmark $bookmark
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('List Bookmarks'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="bookmarks form content">
            <?= $this->Form->create($bookmark) ?>
            <fieldset>
                <legend><?= __('Add Bookmark') ?></legend>
                <?php
                    echo $this->Form->control('title');
                    echo $this->Form->control('url');
                    echo $this->Form->control('description', ['type' => 'textarea']);
                    // Campo para inserir tags como string separada por vírgulas
                    echo $this->Form->control('tag_string', [
                        'label' => 'Tags (separadas por vírgula)',
                        'type' => 'text',
                        'value' => isset($bookmark->tag_string) ? $bookmark->tag_string : ''
                    ]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
