<?php

declare(strict_types=1);

namespace App\Controller;

use Cake\Event\EventInterface;
use Authentication\PasswordHasher\DefaultPasswordHasher;

class UsersController extends AppController
{
    public function index()
    {
        $query = $this->Users->find();
        $users = $this->paginate($query);

        $this->set(compact('users'));
    }

    public function view($id = null)
    {
        $user = $this->Users->get($id, contain: ['Bookmarks']);
        $this->set(compact('user'));
    }

    public function add()
    {
        $user = $this->Users->newEmptyEntity();
        if ($this->request->is('post')) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The user could not be saved. Please, try again.'));
        }
        $this->set(compact('user'));
    }

    public function edit($id = null)
    {
        $user = $this->Users->get($id, contain: []);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The user could not be saved. Please, try again.'));
        }
        $this->set(compact('user'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $user = $this->Users->get($id);
        if ($this->Users->delete($user)) {
            $this->Flash->success(__('The user has been deleted.'));
        } else {
            $this->Flash->error(__('The user could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    public function login()
    {
        $this->request->allowMethod(['get', 'post']);
        $result = $this->Authentication->getResult();

        if ($this->request->is('post')) {
            $data = $this->request->getData();

            $user = $this->Users->find()
                ->where(['email' => $data['email']])
                ->first();

            if ($user) {
                $hasher = new DefaultPasswordHasher();
                if ($hasher->check($data['password'], $user->password)) {
                    $this->Authentication->setIdentity($user);
                    $this->Flash->success('Login efetuado com sucesso!');

                    // Redireciona para os bookmarks do usuário
                    $target = $this->Authentication->getLoginRedirect() ?? ['controller' => 'Bookmarks', 'action' => 'index'];
                    return $this->redirect($target);
                }
            }
        }

        if ($result->isValid()) {
            $target = $this->Authentication->getLoginRedirect() ?? ['controller' => 'Bookmarks', 'action' => 'index'];
            return $this->redirect($target);
        }

        if ($this->request->is('post') && !$result->isValid()) {
            $this->Flash->error(__('Email ou senha inválidos.'));
        }
    }

    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('Authentication.Authentication');
    }

    public function logout()
    {
        $this->Authentication->logout();

        $this->Flash->success('Você saiu da sua conta.');

        return $this->redirect('/users/login');
    }

    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);

        $this->Authentication->allowUnauthenticated(['add', 'login']);
    }

    public function isAuthorized($user)
    {
        return false;
    }
}
