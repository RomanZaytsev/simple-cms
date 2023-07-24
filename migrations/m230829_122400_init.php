<?php

use romanzaytsev\cms\models\Album;
use romanzaytsev\cms\models\Config;
use romanzaytsev\cms\models\Languages;
use romanzaytsev\cms\models\Multimedia;
use romanzaytsev\cms\models\Page;
use romanzaytsev\cms\models\PageBlockParser;
use romanzaytsev\cms\models\PageBlockProperty;
use romanzaytsev\cms\models\PageBlockText;
use romanzaytsev\cms\models\PageTemplate;
use romanzaytsev\cms\models\PageTemplateBlock;
use romanzaytsev\cms\models\Ui;
use romanzaytsev\cms\models\User;
use yii\db\Migration;

/**
 * Class m230829_122400_init
 */
class m230829_122400_init extends Migration
{
    public function up()
    {
        $tableOptions = null;
//        if ($this->db->driverName === 'mysql') {
//            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
//        }

        $this->createTable(Album::tableName(), [
            'id' => $this->primaryKey(11),
            'sort' => $this->integer(11)->defaultValue(0),
            'nameRu' => $this->string(255),
            'nameEn' => $this->string(255),
            'descriptionRu' => $this->text(),
            'descriptionEn' => $this->text(),
            'link' => $this->char(150)->notNull(),
            'preview' => $this->string(255),
        ], $tableOptions);

        $this->createTable(Config::tableName(), [
            'id' => $this->char(150)->notNull(),
            'value' => $this->string(255),
        ], $tableOptions);
        $this->addPrimaryKey('pk', Config::tableName(), 'id');

        $this->createTable(Languages::tableName(), [
            'id' => $this->char(150)->notNull(),
            'value' => $this->string(255),
            'priority' => $this->integer(11)->defaultValue(0),
        ], $tableOptions);
        $this->addPrimaryKey('pk', Languages::tableName(), 'id');
        $this->insert(Languages::tableName(), [
            'id' => 'Ru',
            'value' => 'Rus',
            'priority' => 1,
        ]);
        $this->insert(Languages::tableName(), [
            'id' => 'En',
            'value' => 'Eng',
            'priority' => 2,
        ]);

        $this->createTable(Multimedia::tableName(), [
            'id' => $this->primaryKey(11),
            'albumId' => $this->integer(11),
            'pageId' => $this->integer(11),
            'blockId' => $this->char(150),
            'sort' => $this->integer(11)->defaultValue(0),
            'nameRu' => $this->string(255),
            'nameEn' => $this->string(255),
            'descriptionRu' => $this->text(),
            'descriptionEn' => $this->text(),
            'fullTextRu' => $this->text(),
            'fullTextEn' => $this->text(),
            'link' => $this->string(1000),
            'preview' => $this->string(1000),
            'properties' => $this->text(),
            'href' => $this->string(1000),
            'date' => $this->dateTime(),
        ], $tableOptions);
        $this->createIndex('fk_multimedia_1_idx', Multimedia::tableName(), ['pageId']);
        $this->createIndex('index2', Multimedia::tableName(), ['nameRu']);

        $this->createTable(Page::tableName(), [
            'id' => $this->primaryKey(11),
            'nameRu' => $this->char(150)->notNull()->comment('Наименование'),
            'nameEn' => $this->char(150)->notNull()->comment('Наименование'),
            'link' => $this->char(150)->comment('link'),
            'Date' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP')->comment('ДатаНовости'),
            'datePublications' => $this->dateTime()->comment('ДатаПубликации'),
            'Text' => $this->text()->comment('ТекстНовости'),
            'preview' => $this->text()->comment('АнонсНовости'),
            'publish' => $this->tinyInteger(4)->comment('Опубликовать'),
            'pageTemplateId' => $this->char(36)->comment('ВидНовости'),
            'parentPageId' => $this->integer(),
            'tagDescription' => $this->string(150),
            'views' => $this->integer(10)->unsigned()->notNull()->defaultValue('0')->comment('Количество просмотров'),
            'mainImage' => $this->string()->notNull()->defaultValue('0'),
            'hidden' => $this->integer()->defaultValue('0'),
            'sort' => $this->integer()->defaultValue('0'),
            'parsed_from' => $this->string(),
        ], $tableOptions);
        $this->createIndex('link', Page::tableName(), ['link']);
        $this->createIndex('nameRu', Page::tableName(), ['nameRu']);

        $this->createTable(PageBlockParser::tableName(), [
            'pageId' => $this->integer()->notNull(),
            'blockId' => $this->string(45)->notNull(),
            'url' => $this->text(),
            'selector' => $this->text(),
            'regexp' => $this->text(),
        ], $tableOptions);
        $this->addPrimaryKey('pk', PageBlockParser::tableName(), ['pageId', 'blockId']);

        $this->createTable(PageBlockProperty::tableName(), [
            'pageId' => $this->integer()->notNull(),
            'blockId' => $this->string(45)->notNull(),
            'value' => $this->text(),
        ], $tableOptions);
        $this->addPrimaryKey('pk', PageBlockProperty::tableName(), ['pageId', 'blockId']);

        $this->createTable(PageBlockText::tableName(), [
            'pageId' => $this->integer()->notNull(),
            'blockId' => $this->string(45)->notNull(),
            'valueRu' => $this->text(),
            'valueEn' => $this->text(),
        ], $tableOptions);
        $this->addPrimaryKey('pk', PageBlockText::tableName(), ['pageId', 'blockId']);

        $this->createTable(PageTemplate::tableName(), [
            'id' => $this->char(150)->notNull(),
            'nameRu' => $this->text()->notNull()->comment('Значение'),
            'nameEn' => $this->text()->comment('Значение'),
            'view' => $this->text(),
            'layout' => $this->text(),
            'type' => $this->string(45),
            'ready' => $this->string(45),
        ], $tableOptions);
        $this->addPrimaryKey('pk', PageTemplate::tableName(), 'id');

        $this->createTable(PageTemplateBlock::tableName(), [
            'pageTemplateId' => $this->char(40)->notNull(),
            'id' => $this->string(45)->notNull(),
            'type' => $this->string(45),
            'sort' => $this->integer()->defaultValue('0'),
            'label' => $this->string(250),
            'properties' => $this->text(),
        ], $tableOptions);
        $this->addPrimaryKey('pk', PageTemplateBlock::tableName(), ['pageTemplateId', 'id']);

        $this->createTable(Ui::tableName(), [
            'parentId' => $this->char(80),
            'id' => $this->char(150)->notNull(),
            'valueRu' => $this->string(),
            'valueEn' => $this->string(),
            'href' => $this->string(1024),
            'sort' => $this->integer(),
        ], $tableOptions);
        $this->addPrimaryKey('pk', Ui::tableName(), 'id');

        $this->createTable(User::tableName(), [
            'id' => $this->primaryKey(11),
            'first_name' => $this->string(250),
            'last_name' => $this->string(250),
            'phone_number' => $this->string(30),
            'username' => $this->string(250)->notNull(),
            'email' => $this->string(500),
            'password' => $this->string(250),
            'authKey' => $this->string(250),
            'password_reset_token' => $this->string(250),
            'user_image' => $this->string(500),
        ], $tableOptions);
        $this->addColumn(User::tableName(), 'user_level', "ENUM('Super Admin', 'Admin', 'User') DEFAULT 'User'");
        $this->insert(User::tableName(), [
            'first_name' => 'admin',
            'last_name' => 'admin',
            'phone_number' => '',
            'username' => 'admin',
            'email' => null,
            'password' => '202cb962ac59075b964b07152d234b70',
            'authKey' => 'test100key',
            'password_reset_token' => '',
            'user_image' => '',
            'user_level' => 'Admin',
        ]);
    }

    /**
     * This method contains the logic to be executed when removing this migration.
     */
    public function down()
    {
        $this->dropTable(Album::tableName());
        $this->dropTable(Config::tableName());
        $this->dropTable(Languages::tableName());
        $this->dropTable(Multimedia::tableName());
        $this->dropTable(Page::tableName());
        $this->dropTable(PageBlockParser::tableName());
        $this->dropTable(PageBlockProperty::tableName());
        $this->dropTable(PageBlockText::tableName());
        $this->dropTable(PageTemplate::tableName());
        $this->dropTable(PageTemplateBlock::tableName());
        $this->dropTable(Ui::tableName());
        $this->dropTable(User::tableName());
    }
}
