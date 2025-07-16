<?php

declare(strict_types=1);

namespace App\Controller;

class BookmarksController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();
        // Paginator removido, não será usado
    }

    public function tags()
    {
        $tags = $this->request->getParam('pass');
        $userId = $this->request->getAttribute('identity')->get('id');

        $bookmarks = $this->Bookmarks->find('tagged', [
            'tags' => $tags,
            'user_id' => $userId,
        ])
            ->contain(['Tags']) // opcional, útil para exibir as tags
            ->all();

        $this->set(compact('bookmarks', 'tags'));
    }

    public function index()
    {
        $userId = $this->request->getAttribute('identity')->get('id');

        $bookmarks = $this->Bookmarks->find()
            ->where(['Bookmarks.user_id' => $userId])
            ->contain(['Tags'])
            ->all();

        $this->set(compact('bookmarks'));
    }

    public function view($id = null)
    {
        $bookmark = $this->Bookmarks->get($id, [
            'contain' => ['Users', 'Tags']
        ]);

        $this->set(compact('bookmark'));
    }

    public function add()
    {
        $bookmark = $this->Bookmarks->newEmptyEntity();

        if ($this->request->is('post')) {
            $data = $this->request->getData();

            // Se existir tag_string, converte para array de tags
            if (!empty($data['tag_string'])) {
                $tagsArray = array_map('trim', explode(',', $data['tag_string']));
                $data['tags'] = [];

                foreach ($tagsArray as $tagTitle) {
                    if ($tagTitle !== '') {
                        $data['tags'][] = ['title' => $tagTitle];
                    }
                }
            }

            $bookmark = $this->Bookmarks->patchEntity($bookmark, $data);
            $bookmark->user_id = $this->request->getAttribute('identity')->get('id');

            if ($this->Bookmarks->save($bookmark)) {
                $this->Flash->success('The bookmark has been saved.');
                return $this->redirect(['action' => 'index']);
            }

            $this->Flash->error('The bookmark could not be saved. Please, try again.');
        }

        $tags = $this->Bookmarks->Tags->find('list')->all();
        $this->set(compact('bookmark', 'tags'));
    }

    public function edit($id = null)
    {
        $bookmark = $this->Bookmarks->get($id, ['contain' => ['Tags']]);

        if ($this->request->is(['patch', 'post', 'put'])) {
            $bookmark = $this->Bookmarks->patchEntity($bookmark, $this->request->getData());
            $bookmark->user_id = $this->request->getAttribute('identity')->get('id');

            if ($this->Bookmarks->save($bookmark)) {
                $this->Flash->success(__('The bookmark has been saved.'));
                return $this->redirect(['action' => 'index']);
            }

            $this->Flash->error(__('The bookmark could not be saved. Please, try again.'));
        }

        $tags = $this->Bookmarks->Tags->find('list')->all();
        $this->set(compact('bookmark', 'tags'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $bookmark = $this->Bookmarks->get($id);

        if ($this->Bookmarks->delete($bookmark)) {
            $this->Flash->success(__('The bookmark has been deleted.'));
        } else {
            $this->Flash->error(__('The bookmark could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    public function isAuthorized($user)
    {
        $action = $this->request->getParam('action');

        if (in_array($action, ['index', 'add', 'tags'])) {
            return true;
        }

        $id = $this->request->getParam('pass.0');
        if (!$id) {
            return false;
        }

        $bookmark = $this->Bookmarks->get($id);
        return $bookmark->user_id === $user['id'];
    }
}
