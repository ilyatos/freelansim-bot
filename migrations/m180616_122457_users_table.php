<?php

use yii\db\Migration;

/**
 * Class m180616_122457_users_table
 */
class m180616_122457_users_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('users', [
            'id' => $this->primaryKey(),
            'user_id' => $this->string()->unique()->notNull(),
            'subscribed' => $this->boolean()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('users');
    }

    
}
