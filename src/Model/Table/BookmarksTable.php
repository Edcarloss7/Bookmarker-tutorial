<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class BookmarksTable extends Table
{
    public function findTagged(Query $query, array $options)
    {
        $tags = $options['tags'] ?? [];
        $userId = $options['user_id'] ?? null;

        $query = $query->select(['Bookmarks.id', 'Bookmarks.url', 'Bookmarks.title', 'Bookmarks.description', 'Bookmarks.user_id']);

        if (empty($tags)) {
            $query = $query->leftJoinWith('Tags')
                ->where(['Tags.title IS' => null]);
        } else {
            $query = $query->innerJoinWith('Tags')
                ->where(['Tags.title IN' => $tags]);
        }

        if ($userId) {
            $query = $query->where(['Bookmarks.user_id' => $userId]);
        }

        return $query->group(['Bookmarks.id']);
    }


    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('bookmarks');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
        ]);

        $this->belongsToMany('Tags', [
            'foreignKey' => 'bookmark_id',
            'targetForeignKey' => 'tag_id',
            'joinTable' => 'bookmarks_tags',
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('user_id')
            ->notEmptyString('user_id');

        $validator
            ->scalar('title')
            ->maxLength('title', 50)
            ->allowEmptyString('title');

        $validator
            ->scalar('description')
            ->allowEmptyString('description');

        $validator
            ->scalar('url')
            ->allowEmptyString('url');

        return $validator;
    }

    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['user_id'], 'Users'), ['errorField' => 'user_id']);

        return $rules;
    }

    public function beforeSave($event, $entity, $options)
    {
        if ($entity->tag_string) {
            $entity->tags = $this->_buildTags($entity->tag_string);
        }
    }

    protected function _buildTags($tagString)
    {
        $new = array_unique(array_map('trim', explode(',', $tagString)));
        $out = [];
        $query = $this->Tags->find()
            ->where(['Tags.title IN' => $new])
            ->all();  // <- Adicionado aqui para corrigir o erro

        foreach ($query->extract('title') as $existing) {
            $index = array_search($existing, $new);
            if ($index !== false) {
                unset($new[$index]);
            }
        }

        foreach ($query as $tag) {
            $out[] = $tag;
        }

        foreach ($new as $tag) {
            $out[] = $this->Tags->newEntity(['title' => $tag]);
        }

        return $out;
    }
}
