<?php

namespace app\models\search;

use app\models\History;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * HistorySearch represents the model behind the search form about `app\models\History`.
 *
 * @property array $objects
 */
class HistorySearch extends History
{
    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = History::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['ins_ts' => SORT_DESC, 'id' => SORT_DESC]
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            $query->where('0=1');
            return $dataProvider;
        }

        $query->select(['history.id', 'history.ins_ts', 'history.event', 'history.object', 'history.object_id', 'history.detail', 'history.user_id'])
            ->with([
                'user' => function ($query) {
                    $query->select(['user.id', 'user.username']);
                },
                'task' => function ($query) {
                    $query->select(['task.id', 'task.title']);
                },
                'call' => function ($query) {
                    $query->select(['call.id', 'call.direction', 'call.status']);
                },
                'fax' => function ($query) {
                    $query->select(['fax.id']);
                },
                'sms' => function ($query) {}
            ]);

        return $dataProvider;
    }
}
