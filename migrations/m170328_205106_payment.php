<?php

use yii\db\Migration;

class m170328_205106_payment extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%payment}}', [
            'id' => $this->primaryKey(),
            'variant_id' => $this->integer(),
            'date' => $this->date()->notNull(),
            'sum' => $this->double()->notNull(),
        ], $tableOptions);

        return true;
    }

    public function safeDown()
    {
        $this->dropTable('{{%payment}}');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170328_205106_payment cannot be reverted.\n";

        return false;
    }
    */
}
