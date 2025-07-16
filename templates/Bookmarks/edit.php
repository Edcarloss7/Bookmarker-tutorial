public function edit($id = null)
{
    // Recupera o bookmark com as tags associadas
    $bookmark = $this->Bookmarks->get($id, [
        'contain' => ['Tags']
    ]);

    // Converte as tags atuais em uma string separada por vírgulas para o formulário
    $bookmark->tag_string = implode(', ', collection($bookmark->tags)->extract('title')->toList());

    if ($this->request->is(['patch', 'post', 'put'])) {
        $data = $this->request->getData();

        // Processa a tag_string enviada pelo formulário para array de tags
        if (!empty($data['tag_string'])) {
            $tagsArray = array_map('trim', explode(',', $data['tag_string']));
            $data['tags'] = [];
            foreach ($tagsArray as $tagTitle) {
                if ($tagTitle !== '') {
                    $data['tags'][] = ['title' => $tagTitle];
                }
            }
        } else {
            // Se tag_string estiver vazia, limpa as tags
            $data['tags'] = [];
        }

        $bookmark = $this->Bookmarks->patchEntity($bookmark, $data);
        $bookmark->user_id = $this->Auth->user('id'); // Garante que o bookmark pertence ao usuário logado

        if ($this->Bookmarks->save($bookmark)) {
            $this->Flash->success('The bookmark has been saved.');

            return $this->redirect(['action' => 'index']);
        }
        $this->Flash->error('The bookmark could not be saved. Please, try again.');
    }

    $tags = $this->Bookmarks->Tags->find('list');
    $this->set(compact('bookmark', 'tags'));
}
