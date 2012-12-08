#include <QtScript>
#include <QScriptValueIterator>

#include "transferdialog.h"
#include "webservice.h"


LhcTransferDialog::LhcTransferDialog(int chat_id, QWidget *parent) : QDialog(parent)
{
    this->chatID = chat_id;

    this->setWindowTitle(tr("Chat transfer"));

    usersRadioGroup = new QButtonGroup();

    onlineUsersGroupBox = new QGroupBox(tr("Online users"));    
    onlineUsersBoxLayout = new QVBoxLayout();
    onlineUsersGroupBox->setLayout(onlineUsersBoxLayout);
    
    mainLayout = new QVBoxLayout(this);
    mainLayout->addWidget(onlineUsersGroupBox);
     
    hboxLayout = new QHBoxLayout();    
    okButton = new QPushButton(tr("Ok"),this);
    cancelButton = new QPushButton(tr("Cancel"),this);

    okButton->setObjectName(QString::fromUtf8("okButton"));
    cancelButton->setObjectName(QString::fromUtf8("cancelButton"));

    spacerItem = new QSpacerItem(131, 31, QSizePolicy::Expanding, QSizePolicy::Minimum);
    hboxLayout->addItem(spacerItem);
    hboxLayout->addWidget(okButton);
    hboxLayout->addWidget(cancelButton);

    mainLayout->addLayout(hboxLayout);

    QMetaObject::connectSlotsByName(this);

    LhcWebServiceClient::instance()->LhcSendRequest("/xml/transferchat/"+QString::number(this->chatID),(QObject*) this, LhcTransferDialog::onlineUsersCallback);
}


LhcTransferDialog::~LhcTransferDialog()
{

}

void LhcTransferDialog::on_cancelButton_clicked()
{
    reject();
}

void LhcTransferDialog::on_okButton_clicked()
{	  
    if (this->usersRadioGroup->checkedId() > 0)
    {
        LhcWebServiceClient::instance()->LhcSendRequest("/xml/transferuser/"+QString::number(this->chatID) + "/" + QString::number(this->usersRadioGroup->checkedId()));
    }
    accept();
}

void LhcTransferDialog::onlineUsersCallback(void* pt2Object, QByteArray result)
{
    LhcTransferDialog* mySelf = (LhcTransferDialog*) pt2Object;

    QScriptValue sc; 
    QScriptEngine engine;
    sc = engine.evaluate("("+QString(result)+")");

    QScriptValueIterator itt(sc.property("result"));
    
    while (itt.hasNext()) {
            itt.next();

            if (itt.flags() & QScriptValue::SkipInEnumeration)
                     continue;

            QRadioButton *newButton = new QRadioButton(itt.value().property("name").toString()+" "+itt.value().property("surname").toString());
            mySelf->usersRadioGroup->addButton(newButton,itt.value().property("id").toString().toInt());
            mySelf->onlineUsersBoxLayout->addWidget(newButton);   
        }
    
}
