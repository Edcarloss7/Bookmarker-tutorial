<h1>Login</h1>

<?= $this->Form->create() ?>

<?= $this->Form->control('email', ['label' => 'Email', 'required' => true]) ?>

<?= $this->Form->control('password', ['label' => 'Senha', 'type' => 'password', 'required' => true]) ?>

<?= $this->Form->button('Login') ?>

<?= $this->Form->end() ?>