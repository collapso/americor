<?php

namespace app\models;

use app\models\traits\ObjectNameTrait;
use DateTime;
use Exception;
use stdClass;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%history}}".
 *
 * @property integer $id
 * @property string $ins_ts
 * @property integer $customer_id
 * @property string $event
 * @property string $object
 * @property integer $object_id
 * @property string $message
 * @property string $detail
 * @property integer $user_id
 *
 * @property string $eventText
 *
 * @property Customer $customer
 * @property User $user
 *
 * @property Task $task
 * @property Sms $sms
 * @property Call $call
 * @property Fax $fax
 */
class History extends ActiveRecord
{
    use ObjectNameTrait;

    const EVENT_CREATED_TASK = 'created_task';
    const EVENT_UPDATED_TASK = 'updated_task';
    const EVENT_COMPLETED_TASK = 'completed_task';
    const EVENT_INCOMING_SMS = 'incoming_sms';
    const EVENT_OUTGOING_SMS = 'outgoing_sms';
    const EVENT_INCOMING_CALL = 'incoming_call';
    const EVENT_OUTGOING_CALL = 'outgoing_call';
    const EVENT_INCOMING_FAX = 'incoming_fax';
    const EVENT_OUTGOING_FAX = 'outgoing_fax';
    const EVENT_CUSTOMER_CHANGE_TYPE = 'customer_change_type';
    const EVENT_CUSTOMER_CHANGE_QUALITY = 'customer_change_quality';
    public const EXPORT_HEADER_ROW = ['Date', 'User', 'Type', 'Event', 'Message'];

    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return '{{%history}}';
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['ins_ts'], 'safe'],
            [['customer_id', 'object_id', 'user_id'], 'integer'],
            [['event'], 'required'],
            [['message', 'detail'], 'string'],
            [['event', 'object'], 'string', 'max' => 255],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Customer::class, 'targetAttribute' => ['customer_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'ins_ts' => Yii::t('app', 'Ins Ts'),
            'customer_id' => Yii::t('app', 'Customer ID'),
            'event' => Yii::t('app', 'Event'),
            'object' => Yii::t('app', 'Object'),
            'object_id' => Yii::t('app', 'Object ID'),
            'message' => Yii::t('app', 'Message'),
            'detail' => Yii::t('app', 'Detail'),
            'user_id' => Yii::t('app', 'User ID'),
        ];
    }

    public function getCustomer(): ActiveQuery
    {
        return $this->hasOne(Customer::class, ['id' => 'customer_id']);
    }

    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * @return string[]
     */
    public static function getEventTexts(): array
    {
        return [
            self::EVENT_CREATED_TASK => Yii::t('app', 'Task created'),
            self::EVENT_UPDATED_TASK => Yii::t('app', 'Task updated'),
            self::EVENT_COMPLETED_TASK => Yii::t('app', 'Task completed'),

            self::EVENT_INCOMING_SMS => Yii::t('app', 'Incoming message'),
            self::EVENT_OUTGOING_SMS => Yii::t('app', 'Outgoing message'),

            self::EVENT_CUSTOMER_CHANGE_TYPE => Yii::t('app', 'Type changed'),
            self::EVENT_CUSTOMER_CHANGE_QUALITY => Yii::t('app', 'Property changed'),

            self::EVENT_OUTGOING_CALL => Yii::t('app', 'Outgoing call'),
            self::EVENT_INCOMING_CALL => Yii::t('app', 'Incoming call'),

            self::EVENT_INCOMING_FAX => Yii::t('app', 'Incoming fax'),
            self::EVENT_OUTGOING_FAX => Yii::t('app', 'Outgoing fax'),
        ];
    }

    public static function getEventTextByEvent(string $event): string
    {
        return static::getEventTexts()[$event] ?? $event;
    }

    public function getEventText(): string
    {
        return static::getEventTextByEvent($this->event);
    }

    public function getDetailChangedAttribute(string $attribute): ?stdClass
    {
        $detail = json_decode($this->detail);
        return $detail->changedAttributes->{$attribute} ?? null;
    }

    public function getDetailOldValue(string $attribute): ?string
    {
        $detail = $this->getDetailChangedAttribute($attribute);
        return $detail->old ?? null;
    }

    public function getDetailNewValue(string $attribute): ?string
    {
        $detail = $this->getDetailChangedAttribute($attribute);
        return $detail->new ?? null;
    }

    public function getDetailData(string $attribute): ?string
    {
        $detail = json_decode($this->detail);
        return $detail->data->{$attribute} ?? null;
    }

    /**
     * @return string[]
     * @throws Exception
     */
    public function getExportData(): array
    {
        $createdAt = null === $this->getAttribute('ins_ts')
            ? 'N/A'
            : (new DateTime($this->getAttribute('ins_ts')))->format('M d, Y, g:i:s A');

        return [
            $createdAt,
            $this->user?->getAttribute('username') ??  Yii::t('app', 'System'),
            $this->getAttribute('object'),
            $this->getEventText(),
            strip_tags($this->getEventHtml()),
        ];
    }

    public function getEventHtml(): string
    {
        switch ($this->event) {
            case self::EVENT_CREATED_TASK:
            case self::EVENT_COMPLETED_TASK:
            case self::EVENT_UPDATED_TASK:
                $task = $this->task;
                return "$this->eventText: " . ($task->title ?? '');
            case self::EVENT_INCOMING_SMS:
            case self::EVENT_OUTGOING_SMS:
                return $this->sms->message ? $this->sms->message : '';
            case self::EVENT_OUTGOING_FAX:
            case self::EVENT_INCOMING_FAX:
                return $this->eventText;
            case self::EVENT_CUSTOMER_CHANGE_TYPE:
                return "$this->eventText " .
                    (Customer::getTypeTextByType($this->getDetailOldValue('type')) ?? "not set") . ' to ' .
                    (Customer::getTypeTextByType($this->getDetailNewValue('type')) ?? "not set");
            case self::EVENT_CUSTOMER_CHANGE_QUALITY:
                return "$this->eventText " .
                    (Customer::getQualityTextByQuality($this->getDetailOldValue('quality')) ?? "not set") . ' to ' .
                    (Customer::getQualityTextByQuality($this->getDetailNewValue('quality')) ?? "not set");
            case self::EVENT_INCOMING_CALL:
            case self::EVENT_OUTGOING_CALL:
                /** @var Call $call */
                $call = $this->call;
                return ($call ? $call->totalStatusText . ($call->getTotalDisposition(false) ? " <span class='text-grey'>" . $call->getTotalDisposition(false) . "</span>" : "") : '<i>Deleted</i> ');
            default:
                return $this->eventText;
        }
    }
}
