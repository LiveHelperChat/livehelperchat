#include "privatemessage.h"
#include "ui_privatemessage.h"
#include <QDebug>
#include <QtGui>
#include <QScriptEngine>
#include <QScriptValueIterator>
#include <QTextEdit>
#include "webservice.h"
#include "lhchatsynchro.h"
#include "transferdialog.h"
privatemessage::privatemessage(QWidget *parent) :
    QDialog(parent),
    ui(new Ui::privatemessage)
{
    ui->setupUi(this);
    connect ( ui->pushButton, SIGNAL( clicked() ), this, SLOT( pushButtonClicked() ) );
    connect ( ui->pushButton_2, SIGNAL( clicked() ), this, SLOT( pushButton2Clicked() ) );
}

privatemessage::~privatemessage()
{
    delete ui;
}

void privatemessage::setVisitorID(int id)
{
    this->chat_id = id;
}

void privatemessage::pushButtonClicked() // defined in .h under public/private slots:
{
    QString message = ui->textEdit->toPlainText();

    if (!message.isEmpty())
    {
        QStringList requestString;
        requestString.append("msg="+message);

        LhcWebServiceClient::instance()->LhcSendRequest(requestString,"/xml/sendnotice/"+ QString::number(this->chat_id));
        ui->label_2->setText(tr("Message sent"));
    }
}

void privatemessage::pushButton2Clicked()
{
    this->close();
}

