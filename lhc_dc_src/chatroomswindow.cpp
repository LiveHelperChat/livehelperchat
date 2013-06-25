#include <QDebug>
#include <QtGui>
#include <QScriptEngine>


#include "chatroomswindow.h"
#include "lhtablewidget.h"
#include "chatwindow.h"
#include "privatemessage.h"
#include "onlineuserinfo.h"


ChatRoomsWindow::ChatRoomsWindow( QWidget *parent) : QWidget(parent)
{
	ui.setupUi(this);
    setAttribute(Qt::WA_DeleteOnClose);

    //First time fetching list ballon is not shown
    balloonEnabled = false;

    // For storing pointer to parent window
    this->parentWidget = parent;

    // Main initialization
    ChatRoomstabWidget = new QTabWidget();
    ChatRoomstabWidget->setMinimumHeight(220);
    ChatRoomstabWidget->setMinimumWidth(500);

    // Create tabs
    createPendingChatsTab();
    createActiveChatsTab();
    createClosedChatsTab();
    createOnlineUsersTab();

    // After all tabs created initialize mail layout
    ui.vboxLayout1->addWidget(ChatRoomstabWidget);
    
    // Initial synchronization
    // First time we disable 
    synschronize();

    timer = new QTimer(this);
    connect(timer, SIGNAL(timeout()), this, SLOT(synschronize()));

    connect(pendingChatsList, SIGNAL(customContextMenuRequested (const QPoint &)), this, SLOT(pendingChatsMenu(QPoint)));
    connect(activeChatsList, SIGNAL(customContextMenuRequested (const QPoint &)), this, SLOT(activeChatsMenu(QPoint)));
    connect(closedChatsList, SIGNAL(customContextMenuRequested (const QPoint &)), this, SLOT(closedChatsMenu(QPoint)));
    connect(transferedChatsList, SIGNAL(customContextMenuRequested (const QPoint &)), this, SLOT(transferedChatsMenu(QPoint)));
    connect(OnlineUsersList, SIGNAL(customContextMenuRequested (const QPoint &)), this, SLOT(onlineUsersMenu(QPoint)));

    //Synchronize chats every 10 seconds.
    timer->start(10000);
}

void ChatRoomsWindow::transferedChatsMenu(QPoint p)
{
    QTableWidgetItem *index = transferedChatsList->itemAt(p);
    if (index)
    {
        QMenu *pmenu = new QMenu();
                           
            QAction  *addAct,
                     *newwAct,                  
                     *sepAct;

            addAct = pmenu->addAction( QIcon(":/images/add.png"), tr("Add chat"));
            newwAct  = pmenu->addAction( QIcon(":/images/application_add.png"), tr("Open in a new window"));
            sepAct = pmenu->addAction( QIcon(":/images/application.png"), tr("Open in a new separate window"));
            
            connect(addAct, SIGNAL(triggered()), this, SLOT(addTransferdChatWindow())); 
            connect(newwAct, SIGNAL(triggered()), this, SLOT(newTransferdChatWindow()));
            connect(sepAct, SIGNAL(triggered()), this, SLOT(newTransferdSepChatWindow()));
                      
        pmenu->exec(QCursor::pos());
        delete pmenu;
    }
}



void ChatRoomsWindow::addTransferdChatWindow()
{

    QTableWidgetItem * transferWidget = transferedChatsList->item(transferedChatsList->currentRow(),transferedChatsList->getColumnByName("transfer_id"));  
    if (transferWidget)
    {
        LhcWebServiceClient::instance()->LhcSendRequest("/xml/accepttransfer/"+transferWidget->data(Qt::EditRole).toString());

        ChatWindow *crw = new ChatWindow(transferedChatsList->getCurrentChat());
        crw->setIsTabMode(true);
        crw->setTabIndex(ChatRoomstabWidget->addTab(crw,tr("Loading...")),ChatRoomstabWidget);
		
		this->synschronize();
    }

    
}

void ChatRoomsWindow::newTransferdChatWindow()
{
    QTableWidgetItem * transferWidget = transferedChatsList->item(transferedChatsList->currentRow(),transferedChatsList->getColumnByName("transfer_id")); 

    if (transferWidget)
    {
        LhcWebServiceClient::instance()->LhcSendRequest("/xml/accepttransfer/"+transferWidget->data(Qt::EditRole).toString());

        ChatWindow *crw = new ChatWindow(transferedChatsList->getCurrentChat());
	    mdiArea->addSubWindow(crw);
        crw->setMdiAreas(this->mdiArea);    
        crw->show();
		
		this->synschronize();
    }
}

void ChatRoomsWindow::newTransferdSepChatWindow()
{
    QTableWidgetItem * transferWidget = transferedChatsList->item(transferedChatsList->currentRow(),transferedChatsList->getColumnByName("transfer_id"));
    if (transferWidget)
    {
        LhcWebServiceClient::instance()->LhcSendRequest("/xml/accepttransfer/"+transferWidget->data(Qt::EditRole).toString());

        ChatWindow *crw = new ChatWindow(transferedChatsList->getCurrentChat());
        crw->show();
		
		this->synschronize();
    }
}

void ChatRoomsWindow::activeChatsMenu(QPoint p)
{
    QTableWidgetItem *index = activeChatsList->itemAt(p);
    if (index)
    {
        QMenu *pmenu = new QMenu();
                           
            QAction  *addAct,
                     *newwAct,
                     *closeAct,
                     *deleteAct,
                     *sepAct;

            addAct = pmenu->addAction( QIcon(":/images/add.png"), tr("Add chat"));
            newwAct  = pmenu->addAction( QIcon(":/images/application_add.png"), tr("Open in a new window"));
            sepAct = pmenu->addAction( QIcon(":/images/application.png"), tr("Open in a new separate window"));
            closeAct = pmenu->addAction( QIcon(":/images/cancel.png"), tr("Close chat"));
            deleteAct = pmenu->addAction( QIcon(":/images/delete.png"), tr("Delete chat"));
            
            connect(addAct, SIGNAL(triggered()), this, SLOT(addActiveChatWindow())); 
            connect(newwAct, SIGNAL(triggered()), this, SLOT(newActiveChatWindow()));
            connect(sepAct, SIGNAL(triggered()), this, SLOT(newActiveSepChatWindow()));
            connect(closeAct, SIGNAL(triggered()), this, SLOT(closeActiveChatAction())); 
            connect(deleteAct, SIGNAL(triggered()), this, SLOT(deleteActiveChatAction()));
             
        
        pmenu->exec(QCursor::pos());
        delete pmenu;
    }
}

void ChatRoomsWindow::onlineUsersMenu(QPoint p)
{
    QTableWidgetItem *index = OnlineUsersList->itemAt(p);
    if (index)
    {
        QMenu *pmenu = new QMenu();

            QAction  *sendmsg,
                     *info;
             sendmsg = pmenu->addAction( QIcon(":/images/add.png"), tr("Send a private message"));
            info  = pmenu->addAction( QIcon(":/images/application_add.png"), tr("View user information"));
            connect(sendmsg, SIGNAL(triggered()), this, SLOT(sendMessageWindow()));
            connect(info, SIGNAL(triggered()), this, SLOT(userInfoWindow()));
        pmenu->exec(QCursor::pos());
        delete pmenu;
    }
}

void ChatRoomsWindow::userInfoWindow()
{
    onlineuserinfo *crw = new onlineuserinfo(this,OnlineUsersList->getCurrentChat());

    crw->setVisitorID(OnlineUsersList->getCurrentChat());
    crw->show();
}

void ChatRoomsWindow::sendMessageWindow()
{
    privatemessage *crw = new privatemessage();

    crw->setVisitorID(OnlineUsersList->getCurrentChat());
    crw->show();
}

void ChatRoomsWindow::newActiveSepChatWindow()
{
    ChatWindow *crw = new ChatWindow(activeChatsList->getCurrentChat());
    crw->show();
}

// Open as new tab
void ChatRoomsWindow::addActiveChatWindow()
{
    ChatWindow *crw = new ChatWindow(activeChatsList->getCurrentChat());
    crw->setIsTabMode(true);
    crw->setTabIndex(ChatRoomstabWidget->addTab(crw,tr("Loading...")),ChatRoomstabWidget);
}

void ChatRoomsWindow::newActiveChatWindow()
{
    ChatWindow *crw = new ChatWindow(activeChatsList->getCurrentChat());
	mdiArea->addSubWindow(crw);
    crw->setMdiAreas(this->mdiArea);    
	crw->show();
}

void ChatRoomsWindow::closeActiveChatAction()
{
    LhcWebServiceClient::instance()->LhcSendRequest("/xml/closechat/"+QString::number(activeChatsList->getCurrentChat()));
	this->synschronize();
}

void ChatRoomsWindow::deleteActiveChatAction()
{
    LhcWebServiceClient::instance()->LhcSendRequest("/xml/deletechat/"+QString::number(activeChatsList->getCurrentChat()));
	this->synschronize();
}

void ChatRoomsWindow::closedChatsMenu(QPoint p)
{
    QTableWidgetItem *index = closedChatsList->itemAt(p);
    if (index)
    {
        QMenu *pmenu = new QMenu();
                           
            QAction  *addAct,
                     *newwAct,               
                     *deleteAct,
                     *sepAct;

            addAct = pmenu->addAction( QIcon(":/images/add.png"), tr("Add chat"));
            newwAct  = pmenu->addAction( QIcon(":/images/application_add.png"), tr("Open in a new window")); 
            sepAct = pmenu->addAction( QIcon(":/images/application.png"), tr("Open in a new separate window"));
            deleteAct = pmenu->addAction( QIcon(":/images/delete.png"), tr("Delete chat"));
            
            connect(addAct, SIGNAL(triggered()), this, SLOT(addClosedChatWindow())); 
            connect(newwAct, SIGNAL(triggered()), this, SLOT(newClosedChatWindow()));           
            connect(deleteAct, SIGNAL(triggered()), this, SLOT(deleteClosedChatAction())); 
            connect(sepAct, SIGNAL(triggered()), this, SLOT(newCloseSepChatWindow())); 
        
        pmenu->exec(QCursor::pos());
        delete pmenu;
    }
}

void ChatRoomsWindow::newCloseSepChatWindow()
{
    ChatWindow *crw = new ChatWindow(closedChatsList->getCurrentChat());
    crw->show();
	this->synschronize();
}

void ChatRoomsWindow::addClosedChatWindow()
{
    ChatWindow *crw = new ChatWindow(closedChatsList->getCurrentChat());
    crw->setIsTabMode(true);
    crw->setTabIndex(ChatRoomstabWidget->addTab(crw,tr("Loading...")),ChatRoomstabWidget);
	this->synschronize();
}

void ChatRoomsWindow::newClosedChatWindow()
{
    ChatWindow *crw = new ChatWindow(closedChatsList->getCurrentChat());
	mdiArea->addSubWindow(crw);
    crw->setMdiAreas(this->mdiArea);    
	crw->show();
	this->synschronize();
}

void ChatRoomsWindow::deleteClosedChatAction()
{
    LhcWebServiceClient::instance()->LhcSendRequest("/xml/deletechat/"+QString::number(closedChatsList->getCurrentChat()));
	this->synschronize();
}

void ChatRoomsWindow::pendingChatsMenu(QPoint p)
{
    QTableWidgetItem *index = pendingChatsList->itemAt(p);
    if (index)
    {
        QMenu *pmenu = new QMenu();
                           
            QAction  *acceptAct,
                     *newwAct,
                     *denyAct,
                     *sepAct;

            acceptAct = pmenu->addAction( QIcon(":/images/accept.png"), tr("Accept chat"));
            newwAct  = pmenu->addAction( QIcon(":/images/application_add.png"), tr("Open in a new window"));
            sepAct = pmenu->addAction( QIcon(":/images/application.png"), tr("Open in a new separate window"));
            denyAct = pmenu->addAction( QIcon(":/images/cancel.png"), tr("Reject chat"));
            

            connect(acceptAct, SIGNAL(triggered()), this, SLOT(acceptPendingChatWindow())); 
            connect(newwAct, SIGNAL(triggered()), this, SLOT(newPendingChatWindow()));
            connect(denyAct, SIGNAL(triggered()), this, SLOT(denyPendingChatAction()));
            connect(sepAct, SIGNAL(triggered()), this, SLOT(newPendingSepChatWindow())); 
        
        pmenu->exec(QCursor::pos());
        delete pmenu;
    }
}

void ChatRoomsWindow::newPendingSepChatWindow()
{
    ChatWindow *crw = new ChatWindow(pendingChatsList->getCurrentChat());
    crw->show();
	this->synschronize();
}

void ChatRoomsWindow::denyPendingChatAction()
{
    LhcWebServiceClient::instance()->LhcSendRequest("/xml/deletechat/"+QString::number(pendingChatsList->getCurrentChat()));
	this->synschronize();
}

void ChatRoomsWindow::acceptPendingChatWindow()
{
    ChatWindow *crw = new ChatWindow(pendingChatsList->getCurrentChat());
    crw->setIsTabMode(true);
    crw->setTabIndex(ChatRoomstabWidget->addTab(crw,tr("Loading...")),ChatRoomstabWidget);
	this->synschronize();
}

void ChatRoomsWindow::newPendingChatWindow()
{
    ChatWindow *crw = new ChatWindow(pendingChatsList->getCurrentChat(),this);
	mdiArea->addSubWindow(crw);
    crw->setMdiAreas(this->mdiArea);    
	crw->show();
	this->synschronize();
}

void ChatRoomsWindow::synschronize()
{
    LhcWebServiceClient *lhwsc = LhcWebServiceClient::instance();
    lhwsc->LhcSendRequest("/xml/lists/",(QObject*) this, ChatRoomsWindow::receivedDataCallback);
}

void ChatRoomsWindow::createOnlineUsersTab()
{

    // Create tab layout
    OnlineUsersGroupBox = new QGroupBox(tr("Online Users"));
    OnlineUsersListVBOX = new QVBoxLayout;

    OnlineUsersList = new LHQTableWidget(this);
    OnlineUsersList->setEditTriggers(QAbstractItemView::NoEditTriggers);
    OnlineUsersList->setSelectionBehavior(QAbstractItemView::SelectRows);
    OnlineUsersList->setSelectionMode(QAbstractItemView::SingleSelection);
    OnlineUsersList->verticalHeader()->hide();
    OnlineUsersList->setAlternatingRowColors(true);
    OnlineUsersList->setContextMenuPolicy(Qt::CustomContextMenu);

    OnlineUsersListVBOX->addWidget(OnlineUsersList);
    OnlineUsersGroupBox->setLayout(OnlineUsersListVBOX);

    // Initialize main tab layout
    OnlineUsersDataVBOX = new QVBoxLayout;
    OnlineUsersDataVBOX->addWidget(OnlineUsersGroupBox);

    // Create tab container
    tabOnlineUsers = new QWidget();
    tabOnlineUsers->setLayout(OnlineUsersDataVBOX);

    // Add tab
    ChatRoomstabWidget->addTab(tabOnlineUsers,tr("Online Users"));
}

void ChatRoomsWindow::createPendingChatsTab()
{  
    // Create transfered chats groupbox
    transferedChatsGroupBox = new QGroupBox(tr("Transferred chats"));
    transferedChatsListVBOX = new QVBoxLayout;

    // Table transfered chats
    transferedChatsList = new LHQTableWidget(this);    
    transferedChatsList->setEditTriggers(QAbstractItemView::NoEditTriggers); 
    transferedChatsList->setSelectionBehavior(QAbstractItemView::SelectRows);
    transferedChatsList->setSelectionMode(QAbstractItemView::SingleSelection);
    transferedChatsList->verticalHeader()->hide();
    transferedChatsList->setAlternatingRowColors(true);
    transferedChatsList->setContextMenuPolicy(Qt::CustomContextMenu);

    // Add table to layout
    transferedChatsListVBOX->addWidget(transferedChatsList);  
    transferedChatsGroupBox->setLayout(transferedChatsListVBOX);

    // Create transfered cats group box
    pendingChatsGroupBox = new QGroupBox(tr("Pending chats"));
    pendingChatsListVBOX = new QVBoxLayout;

    pendingChatsList = new LHQTableWidget(this);    
    pendingChatsList->setEditTriggers(QAbstractItemView::NoEditTriggers); 
    pendingChatsList->setSelectionBehavior(QAbstractItemView::SelectRows);
    pendingChatsList->setSelectionMode(QAbstractItemView::SingleSelection);
    pendingChatsList->verticalHeader()->hide();
    pendingChatsList->setAlternatingRowColors(true);
    pendingChatsList->setContextMenuPolicy(Qt::CustomContextMenu);

    pendingChatsListVBOX->addWidget(pendingChatsList);
    pendingChatsGroupBox->setLayout(pendingChatsListVBOX);
    
    // Initialize main tab layout
    pendingDataVBOX = new QVBoxLayout;
    //pendingDataVBOX->addWidget(pendingChatsGroupBox);
    //pendingDataVBOX->addWidget(transferedChatsGroupBox);

    QSplitter *splitter = new QSplitter(Qt::Vertical,this);
    splitter->addWidget(pendingChatsGroupBox);
    splitter->addWidget(transferedChatsGroupBox);

    pendingDataVBOX->addWidget(splitter);
   
    // Create tab container and set layout
    tabPendingChats = new QWidget();  
    tabPendingChats->setLayout(pendingDataVBOX);
    
    // Add tab 
    ChatRoomstabWidget->addTab(tabPendingChats,tr("Pending chats"));
}

void ChatRoomsWindow::receivedDataCallback(void* pt2Object, QByteArray result)
{
    //qDebug("value %s",QString(result).toStdString().c_str());
    ChatRoomsWindow* mySelf = (ChatRoomsWindow*) pt2Object;
    QScriptValue sc; 
    QScriptEngine engine;
    sc = engine.evaluate("("+QString(result)+")");
    mySelf->pendingChatsList->setData(sc.property("pending_chats"));
    mySelf->closedChatsList->setData(sc.property("closed_chats"));
    mySelf->activeChatsList->setData(sc.property("active_chats"));
    mySelf->transferedChatsList->setData(sc.property("transfered_chats"));
    mySelf->OnlineUsersList->setData(sc.property("online_users"));

    // Avoid tooltips on initial request
    if (mySelf->balloonEnabled == false)
    {
        mySelf->transferedChatsList->setTableMode(1);
        mySelf->pendingChatsList->setTableMode(0);

        connect(mySelf->transferedChatsList, SIGNAL(newChatAdded(int,int)), mySelf->parentWidget, SLOT(showToolTipNewChat(int,int)));
        connect(mySelf->pendingChatsList, SIGNAL(newChatAdded(int,int)), mySelf->parentWidget, SLOT(showToolTipNewChat(int,int)));

        mySelf->balloonEnabled = true;
    }
}

void ChatRoomsWindow::createClosedChatsTab()
{
    // Create tab layout
    closedChatsGroupBox = new QGroupBox(tr("Closed chats"));
    closedChatsListVBOX = new QVBoxLayout;
    
    //Pending chats listing
    closedChatsList = new LHQTableWidget(this);    
    closedChatsList->setEditTriggers(QAbstractItemView::NoEditTriggers); 
    closedChatsList->setSelectionBehavior(QAbstractItemView::SelectRows);
    closedChatsList->setSelectionMode(QAbstractItemView::SingleSelection);
    closedChatsList->verticalHeader()->hide();
    closedChatsList->setAlternatingRowColors(true);
    closedChatsList->setContextMenuPolicy(Qt::CustomContextMenu);
    // It's also posible to populate like this. But now we send single request for all four lists.
    //pendingChatsList->setQuery("/xml/closedchats/");
   
    closedChatsListVBOX->addWidget(closedChatsList);
    closedChatsGroupBox->setLayout(closedChatsListVBOX);


    // Initialize main tab layout
    closedDataVBOX = new QVBoxLayout;
    closedDataVBOX->addWidget(closedChatsGroupBox);
  

    // Create tab container
    tabClosedChats = new QWidget();  
    tabClosedChats->setLayout(closedDataVBOX);

     // Add tab 
    ChatRoomstabWidget->addTab(tabClosedChats,tr("Closed chats"));   
}

void ChatRoomsWindow::createActiveChatsTab()
{
    // Create tab layout
    activeChatsGroupBox = new QGroupBox(tr("Active chats"));
    activeChatsListVBOX = new QVBoxLayout;

    activeChatsList = new LHQTableWidget(this);    
    activeChatsList->setEditTriggers(QAbstractItemView::NoEditTriggers); 
    activeChatsList->setSelectionBehavior(QAbstractItemView::SelectRows);
    activeChatsList->setSelectionMode(QAbstractItemView::SingleSelection);
    activeChatsList->verticalHeader()->hide();
    activeChatsList->setAlternatingRowColors(true);
    activeChatsList->setContextMenuPolicy(Qt::CustomContextMenu);

    activeChatsListVBOX->addWidget(activeChatsList);
    activeChatsGroupBox->setLayout(activeChatsListVBOX);

    // Initialize main tab layout
    activeDataVBOX = new QVBoxLayout;
    activeDataVBOX->addWidget(activeChatsGroupBox);
      
    // Create tab container
    tabActiveChats = new QWidget();  
    tabActiveChats->setLayout(activeDataVBOX);

    // Add tab 
    ChatRoomstabWidget->addTab(tabActiveChats,tr("Active chats")); 
}

ChatRoomsWindow::~ChatRoomsWindow()
{

}

void ChatRoomsWindow::setMdiAreas(QMdiArea *mdiMainArea)
{    
    this->mdiArea = mdiMainArea;
}
