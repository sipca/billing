<?php

namespace frontend\models;

use common\models\ClientNumber;
use common\models\Line;
use PAMI\Client\Impl\ClientImpl;
use PAMI\Message\Action\OriginateAction;
use PhpOffice\PhpSpreadsheet\IOFactory;
use yii\base\Model;
use yii\web\UploadedFile;

class DialerForm extends Model
{
    public $numbers;
    public $file;
    public $lines;

    public function rules()
    {
        return [
            [['lines'], 'required'],
            [['numbers', 'lines'], 'safe'],
            [['file'], 'file', 'skipOnEmpty' => true],
        ];
    }

    public function dial()
    {
        $dialer_trunk = env('DIALER_TRUNK');
        $dialer_context = env('DIALER_CONTEXT');
        $driver = env("AST_DRIVER", "PJSIP");

        $options = [
            'host' => env("AMI_HOST"),
            'port' => 5038,
            'username' => env("AMI_USERNAME"),
            'secret' => env("AMI_SECRET"),
            'connect_timeout' => 10,
            'read_timeout' => 100
        ];

        $client = new ClientImpl($options);
        $client->open();

        $numbers1 = $this->parseFile();
        $numbers2 = []; //$this->parseNumbers();
        $numbers = array_merge($numbers1, $numbers2);

        $extString = $extOnlyNumString = "";
        $lines = Line::find()
            ->where(["id" => $this->lines])
            ->all();

        foreach ($lines as $line) {
            if($extString) {
                $extString .= "&";
            }
            if($extOnlyNumString) {
                $extOnlyNumString .= ",";
            }
            $extString .= $driver."/" .$line->sip_num;
            $extOnlyNumString .= $line->sip_num;
        }

        foreach ($numbers as $phone => $name) {

            $channel = "$driver/$phone@$dialer_trunk";

            $originate = new OriginateAction($channel);
            $originate->setContext($dialer_context);
            $originate->setExtension('s');
            $originate->setCallerId($dialer_trunk);
            $originate->setPriority(1);
            $originate->setAsync(true);
            $originate->setTimeout(20000);
            $originate->setVariable('CLIENT_NAME', $name);
            $originate->setVariable('CALLER_ID_NUMBER', $phone);
            $originate->setVariable('OPERATORS', $extString);

//            \Yii::debug($originate->serialize());

            $client->send($originate);
            usleep(500000);
        }

    }

    private function parseFile()
    {
        /** @var UploadedFile $file */
        $file = $this->file;

        $spreadsheet = IOFactory::load($file->tempName);
        $sheet = $spreadsheet->getActiveSheet();

        $rows = $sheet->toArray(null, true, true, true);

        $result = [];

        foreach ($rows as $index => $row) {
//            if ($index === 1) {
//                continue; // пропускаем заголовок
//            }

            $name  = trim($row['A']); // столбец A — Имя
            $phone = preg_replace('/\D+/', '', $row['B']); // столбец B — Телефон (оставляем только цифры)

            if (!empty($name) && !empty($phone)) {
                $result[$phone] = $name;
            }
        }

        \Yii::debug($result);

        return $result;
    }

    private function parseNumbers()
    {
        return array_filter(array_map('trim', explode("\n", $this->numbers)));
    }

    public function save()
    {
        $numbers = $this->parseFile();
        foreach ($numbers as $phone => $name) {
            $model = new ClientNumber([
                "number" => (string) $phone,
                "name" => $name,
            ]);
            $model->save();
        }
    }
}