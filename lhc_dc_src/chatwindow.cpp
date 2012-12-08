#include <QDebug>
#include <QtGui>
#include <QScriptEngine>
#include <QScriptValueIterator>
#include <QTextEdit>


#include "chatwindow.h"
#include "webservice.h"
#include "lhchatsynchro.h"
#include "transferdialog.h"


ChatWindow::ChatWindow(int chat_id, QWidget *parent) : QWidget(parent)
{
	ui.setupUi(this);
    setAttribute(Qt::WA_DeleteOnClose); 

    /**
    * Avoids closing whole application if main window is hidden.
    */
    setAttribute(Qt::WA_QuitOnClose,false);


    this->chatID = chat_id;

    this->asTab = false;
    this->separateWindow = false;

    this->chatRoomsParent = 0;
    this->tabIndex = 0;
    this->mdiArea = 0;

    actionsGroupBox = new QGroupBox(tr("Actions"));  
    actionButtonsLayout = new QHBoxLayout();
    closeDialogButton = new QPushButton();
    closeChatButton = new QPushButton();
    deleteChatButton = new QPushButton();
    transferChatButton = new QPushButton();
    separateWindowButton = new QPushButton();

    closeDialogButton->setProperty("iconButton", true);
    closeChatButton->setProperty("iconButton", true);
    deleteChatButton->setProperty("iconButton", true);
    transferChatButton->setProperty("iconButton", true);
    separateWindowButton->setProperty("iconButton", true);

    closeDialogButton->setIcon(QIcon(":/images/application_delete.png"));
    closeChatButton->setIcon(QIcon(":/images/cancel.png"));
    deleteChatButton->setIcon(QIcon(":/images/delete.png"));
    transferChatButton->setIcon(QIcon(":/images/user_go.png"));
    separateWindowButton->setIcon(QIcon(":/images/application.png"));

    actionButtonsLayout->addWidget(closeDialogButton);
    actionButtonsLayout->addWidget(closeChatButton);
    actionButtonsLayout->addWidget(deleteChatButton);
    actionButtonsLayout->addWidget(transferChatButton);
    actionButtonsLayout->addWidget(separateWindowButton);
    actionButtonsLayout->setSpacing(1);
    actionsGroupBox->setLayout(actionButtonsLayout);
  
    connect(closeDialogButton, SIGNAL(clicked()), this, SLOT(closeButtonClicked()));
    connect(closeChatButton, SIGNAL(clicked()), this, SLOT(closeChatClicked()));
    connect(deleteChatButton, SIGNAL(clicked()), this, SLOT(deleteChatClicked()));
    connect(separateWindowButton, SIGNAL(clicked()), this, SLOT(separateWindowClicked()));
    connect(transferChatButton, SIGNAL(clicked()), this, SLOT(transferChatClicked()));


    informationGroupBox = new QGroupBox(tr("Information"));
    inforChat = new QLabel(tr("Loading..."));
    infoGroupBoxLayout = new QVBoxLayout;
    infoGroupBoxLayout->addWidget(inforChat);
    informationGroupBox->setLayout(infoGroupBoxLayout);


    ownerGroupBox = new QGroupBox(tr("Owner"));
    infoOwner = new QLabel(tr("Loading..."));
    infoOwnerGroupBoxLayout = new QVBoxLayout;
    infoOwnerGroupBoxLayout->addWidget(infoOwner);
    ownerGroupBox->setLayout(infoOwnerGroupBoxLayout);


    mainGrindLayout = new QGridLayout();
    mainGrindLayout->addWidget(actionsGroupBox,0,0);
    mainGrindLayout->addWidget(informationGroupBox,0,1);
    mainGrindLayout->addWidget(ownerGroupBox,0,2);

    mainGrindLayout->setColumnStretch(0,0);
    mainGrindLayout->setColumnStretch(1,5000);
    mainGrindLayout->setColumnStretch(2,0);


    messagesText = new QTextEdit();
    newmessageText = new LHCTextEdit(this->chatID);
    
    QSplitter *splitter = new QSplitter(Qt::Vertical,this);
    splitter->addWidget(messagesText);
    splitter->addWidget(newmessageText);

    splitter->setStretchFactor ( 0, 5000 );
    splitter->setStretchFactor ( 1, 1 );    
    splitter->setOpaqueResize(false);

    // Add main layout
    mainGrindLayout->addWidget(splitter,1,0,1,3);

    ui.vboxLayout1->addLayout(mainGrindLayout);


    audioOutput = new Phonon::AudioOutput(Phonon::NotificationCategory, this);
    mediaObject = new Phonon::MediaObject(this);
    Phonon::createPath(mediaObject, audioOutput);

    connect(mediaObject, SIGNAL(stateChanged(Phonon::State,Phonon::State)),
                 this, SLOT(stateChanged(Phonon::State,Phonon::State)));

    // Initial reques, actualy we could take data from parent window. But we have to update it's state so we send initial request.
    LhcWebServiceClient::instance()->LhcSendRequest("/xml/chatdata/"+QString::number(this->chatID),(QObject*) this, ChatWindow::getDataChat);

}

void ChatWindow::stateChanged(Phonon::State newState, Phonon::State /* oldState */)
 {
     switch (newState) {
         case Phonon::ErrorState:
             if (mediaObject->errorType() == Phonon::FatalError) {
                 QMessageBox::warning(this, tr("Fatal Error"),
                 mediaObject->errorString());
             } else {
                 QMessageBox::warning(this, tr("Error"),
                 mediaObject->errorString());
             }
             break;
         case Phonon::PlayingState:

                 break;
         case Phonon::StoppedState:

                 break;
         case Phonon::PausedState:

                 break;
         case Phonon::BufferingState:
                 break;
         default:
             ;
     }
 }

void ChatWindow::transferChatClicked()
{
    LhcTransferDialog *dialog = new LhcTransferDialog(this->chatID,this);
    dialog->exec();

    // Perhaps we will show some status here
    /*if (dialog->exec() == QDialog::Accepted) { 
        qDebug("Accepted");
    } else {
        qDebug("Not accepted");
    }*/

    delete dialog;
}

void ChatWindow::setTabIndex(int index,QTabWidget *widget)
{
   this->tabIndex = index;
   this->chatRoomsParent = widget;
}

void ChatWindow::setIsTabMode(bool tabMode)
{
    this->asTab = tabMode;
}

ChatWindow::~ChatWindow()
{
    // No need to notife lhcChatSynchro Because it'self does all dirty job.
}

void ChatWindow::separateWindowClicked()
{
    if (this->mdiArea)
    {      
        QMdiSubWindow *currentSub = this->mdiArea->activeSubWindow();
        if (currentSub){           
            this->mdiArea->removeSubWindow(currentSub);
            currentSub->show();
        }

        this->separateWindow = true;
    }

    if (this->asTab)
    {
        this->chatRoomsParent->removeTab(this->tabIndex);

        //Neccesary !!!
        this->setParent(0);
        this->show();
        this->asTab = false;
        this->separateWindow = true;
    }

}

void ChatWindow::closeButtonClicked()
{
    if (this->mdiArea)
        this->mdiArea->closeActiveSubWindow();

    this->close();      
}

void ChatWindow::closeChatClicked()
{
    LhcWebServiceClient::instance()->LhcSendRequest("/xml/closechat/"+QString::number(this->chatID));

    if (this->mdiArea)
        this->mdiArea->closeActiveSubWindow();

    this->close(); 
}

void ChatWindow::deleteChatClicked()
{
    LhcWebServiceClient::instance()->LhcSendRequest("/xml/deletechat/"+QString::number(this->chatID));

    if (this->mdiArea)
        this->mdiArea->closeActiveSubWindow();

    this->close();
}

void ChatWindow::receivedMessages(void* pt2Object, QScriptValue result)
{
    ChatWindow* mySelf = (ChatWindow*) pt2Object;
  
    //qDebug("Received message %s",result.toString().toStdString().c_str());
    QString msgRow = mySelf->messagesText->toPlainText().isEmpty() ? "" : mySelf->messagesText->toHtml();

    if (mySelf->newmessageText->messageSend == false) {
        if ( QFile::exists(qApp->applicationDirPath() + "/sounds/new_message.mp3") ) {
            mySelf->mediaObject->setCurrentSource(Phonon::MediaSource(qApp->applicationDirPath() + "/sounds/new_message.mp3"));
            mySelf->mediaObject->play();
        }
    }

    mySelf->newmessageText->messageSend = false;

    // Chat messages
    if (result.isArray())
    {
        QString msgBackground = "";
        QString msgText = "";

        QScriptValueIterator itt(result);
        QDateTime dateTime2 = QDateTime();

        while (itt.hasNext()) {
                itt.next(); 

                if (itt.flags() & QScriptValue::SkipInEnumeration)
                         continue;

                if (itt.value().property("user_id").toString() == "0")
                {
                    msgBackground = "background-color:#EEECF5";
                } else {
                    msgBackground = "";   
                }

                msgRow += "<p style=\"margin:0px;"+msgBackground+"\"><div class=\"message-row";

                if (itt.value().property("user_id").toString() == "0") msgRow += " "+tr("responsable");

                dateTime2.setTime_t(itt.value().property("time").toString().toInt());

                msgRow += "\"><div class=\"msg-date\">"+dateTime2.toString("yyyy.MM.dd hh:mm:ss") + " </div><span style=\"color:#222222;font-weight:600;\">";
            
                if (itt.value().property("user_id").toString() == "0") 
                    msgRow += mySelf->clientNick;
                else
                    msgRow += itt.value().property("name_support").toString();

                msgText = itt.value().property("msg").toString();
                msgText.replace(QRegExp("\n"), "<br>\n");

                msgRow += ":</span> <span>"+ msgText +"</span></div></p>"; 
        }
    // Chat status information
    } else {
        msgRow +="<p style=\"margin:0px;\">"+tr(result.toString().toUtf8())+"</p>";
    }

    mySelf->messagesText->setHtml(msgRow);

    // Scroll to bottom
    QScrollBar *sb = mySelf->messagesText->verticalScrollBar();
    sb->setValue(sb->maximum());
   

}


void ChatWindow::setMdiAreas(QMdiArea *mdiMainArea)
{    
    this->mdiArea = mdiMainArea;
}

void ChatWindow::getDataChat(void* pt2Object, QByteArray result)
{

    //{"error":false,"chat":{"id":"8","nick":"fun","status":"0","time":"1249210772","user_id":"0","hash":"7b90214caa2b0349cebf47ae293a3d45196e8958","ip":"62.80.233.34","referrer":"http:\/\/livehelperchat.com\/","dep_id":"1","email":"remdex@remdex.info","user_status":"0","support_informed":"0"}}
    //qDebug("Received chat data %s",QString(result).toStdString().c_str());

    ChatWindow* mySelf = (ChatWindow*) pt2Object;

    QScriptValue sc; 
    QScriptEngine engine;
    sc = engine.evaluate("("+QString(result)+")");
 
    if (sc.property("error").toBoolean() == false)
    {      
        QScriptValue chat = sc.property("chat");
        mySelf->inforChat->setText("<b>IP</b> - "+chat.property("ip").toString() + ", <b>"+tr("Come from")+"</b> - "+chat.property("referrer").toString()+",<br/> <b>ID</b> - "+chat.property("id").toString()+", <b>"+tr("E-mail")+"</b> - "+chat.property("email").toString());
        mySelf->infoOwner->setText(sc.property("ownerstring").toString());          
        mySelf->clientNick = chat.property("nick").toString();
       // qDebug("Assigned %s ",mySelf->chatScriptObject.property("ip").toString().toStdString().c_str());
        
        if (mySelf->tabIndex > 0)
        {
            mySelf->chatRoomsParent->setTabText(mySelf->tabIndex, mySelf->clientNick);
        }

        LhcChatSynchro::instance()->addChatToSynchro(chat.property("id").toString().toInt(),1,(QObject*) mySelf, ChatWindow::receivedMessages);

    } else {
        QMessageBox::warning(NULL, tr("Error"),
									 sc.property("error_string").toString(),
									 tr("&OK"), QString::null , 0, 0, 1);
    }

    

}

