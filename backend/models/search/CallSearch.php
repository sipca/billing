<?php

namespace backend\models\search;

use kartik\daterange\DateRangeBehavior;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Call;

/**
 * CallSearch represents the model behind the search form of `common\models\Call`.
 */
class CallSearch extends Call
{
    public $date_start;
    public $date_end;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'tariff_id', 'duration', 'billing_duration', 'status',  'updated_at'], 'integer'],
            [['call_id', 'source', 'destination', 'record_link', 'created_at', 'direction', 'date_start', 'date_end', 'line_id'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function behaviors() {
        return [
            [
                'class' => DateRangeBehavior::className(),
                'attribute' => 'created_at',
                'dateStartAttribute' => 'date_start',
                'dateEndAttribute' => 'date_end',
                'dateStartFormat' => 'd-m-Y',
                'dateEndFormat' => 'd-m-Y',

            ]
        ];
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     * @param string|null $formName Form name to be used into `->load()` method.
     *
     * @return ActiveDataProvider
     */
    public function search($params, $formName = null)
    {
        $query = Call::find()
            ->joinWith([
                "line.tariff",
                "tariff.prefixes",
            ]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params, $formName);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if($this->line_id) {
            $lines = explode(",", $this->line_id);
            if($lines) {
                $query->andFilterWhere(["in", "line.name", $lines]);
            }
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'call.tariff_id' => $this->tariff_id,
            'duration' => $this->duration,
            'billing_duration' => $this->billing_duration,
            'call.status' => $this->status,
            'updated_at' => $this->updated_at,
        ]);

        if($this->created_at) {
            $query->andFilterWhere(['between', 'call.created_at', Yii::$app->formatter->asTimestamp($this->date_start), Yii::$app->formatter->asTimestamp($this->date_end)]);
        }

        $query
            ->andFilterWhere(['like', 'call_id', $this->call_id])
            ->andFilterWhere(['like', 'source', $this->source])
            ->andFilterWhere(['like', 'destination', $this->destination])
            ->andFilterWhere(['like', 'record_link', $this->record_link])
            ->andFilterWhere(['like', 'direction', $this->direction]);

        return $dataProvider;
    }
}
