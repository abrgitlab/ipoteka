<?php

use yii\db\Migration;

class m170328_205056_variant extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%variant}}', [
            'id' => $this->primaryKey(),
            'sum' => $this->double()->notNull(),
            'percent' => $this->float()->notNull(),
            'start_date' => $this->date()->notNull(),
            'period' => $this->integer()->notNull(),
            'created_at' => $this->timestamp()->notNull(),
            'updated_at' => $this->timestamp()->notNull(),
        ], $tableOptions);

        return true;
    }

    public function safeDown()
    {
        $this->dropTable('{{%variant}}');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170328_205056_variant cannot be reverted.\n";

        return false;
    }
    */
}
