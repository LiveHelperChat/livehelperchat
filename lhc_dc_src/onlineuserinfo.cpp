#include "onlineuserinfo.h"
#include "ui_onlineuserinfo.h"
#include "webservice.h"
#include <QTableWidget>
#include <QScriptValueIterator>
#include <QScriptEngine>
#include <QTextEdit>
#include <QtGui>

onlineuserinfo::onlineuserinfo(QWidget *parent,int chat_id) :
    QDialog(parent),
    ui(new Ui::onlineuserinfo)
{
    this->chat_id = chat_id;
    ui->setupUi(this);

    connect( ui->pushButton, SIGNAL( clicked() ), this, SLOT( pushButtonClicked() ) );

    LhcWebServiceClient::instance()->LhcSendRequest("/xml/userinfo/"+QString::number(this->chat_id),(QObject*) this, onlineuserinfo::getVisitorData);
}

onlineuserinfo::~onlineuserinfo()
{
    delete ui;
}

void onlineuserinfo::setVisitorID(int id)
{
    this->chat_id = id;
}

void onlineuserinfo::pushButtonClicked()
{
    this->close();
}

void onlineuserinfo::getVisitorData(void* pt2Object, QByteArray result)
{
   QScriptValue sc;
   QScriptEngine engine;
   sc = engine.evaluate("("+QString(result)+")");
   QScriptValue chat = sc.property("user");
   onlineuserinfo* mySelf = (onlineuserinfo*) pt2Object;
   mySelf->ui->textEdit->setHtml(chat.toString());
}
