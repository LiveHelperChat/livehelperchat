#include <QtGui>
#include <QtPlugin>
#include <QWorkspace>
#include <Qt>

#include "mainwindow.h"
#include "logindialog.h"
#include "chatroomswindow.h"
#include "chatwindow.h"
#include "webservice.h"

/**
*Class constructor
**/
MainWindow::MainWindow()
{
    //QApplication::setStyle(QStyleFactory::create("cleanlooks"));

	mdiArea = new QMdiArea;
    setCentralWidget(mdiArea);

    setWindowIcon(QIcon(":/images/icon.png"));
	setWindowTitle(tr("Live helper chat"));

	createActions();
	createTrayIcon();
	createMainMenu();

	// Try icon changing
	connect(trayIcon, SIGNAL(activated(QSystemTrayIcon::ActivationReason)),
            this, SLOT(iconActivated(QSystemTrayIcon::ActivationReason)));

    connect(trayIcon, SIGNAL(messageClicked()), this, SLOT(messageClicked()));


	trayIcon->show();

	this->createStatusBar();

    QFile file(qApp->applicationDirPath() + "/qss/lhc.qss");
    file.open(QFile::ReadOnly);
    QString styleSheet = QLatin1String(file.readAll());
    qApp->setStyleSheet(styleSheet);

    this->chatID = 0;
    this->chatMode = 0;

    audioOutput = new Phonon::AudioOutput(Phonon::NotificationCategory, this);
    mediaObject = new Phonon::MediaObject(this);
    Phonon::createPath(mediaObject, audioOutput);

    this->chatRooms();

    this->getOnlineStatus();
}


void MainWindow::ChangeStatusBar(const QString &newStatus)
{
	statusLabel->setText(newStatus);
}

void MainWindow::getOnlineStatus()
{
    LhcWebServiceClient::instance()->LhcSendRequest("/xml/getuseronlinestatus/",(QObject*) this, MainWindow::parseOnlineStatus);
}

void MainWindow::parseOnlineStatus(void* pt2Object, QByteArray result)
{
    MainWindow* mySelf = (MainWindow*) pt2Object;

    QScriptValue sc;
    QScriptEngine engine;
    sc = engine.evaluate("("+QString(result)+")");

    if (sc.property("online").toBoolean() == false) {
        mySelf->onlineofflineAct->setChecked(true);
    } else {
        mySelf->onlineofflineAct->setChecked(false);
    }
}

/**
*
*/
void MainWindow::showToolTipNewChat(int chat_id, int chat_mode)
{
    QSystemTrayIcon::MessageIcon icon = QSystemTrayIcon::MessageIcon(QStyle::SP_MessageBoxInformation);

    if (chat_mode == 0)
    {
        trayIcon->showMessage(tr("New request"), tr("You have a new chat pending. To start the chat click me."),icon , 15 * 1000);
    }

    if (chat_mode == 1)
    {
        trayIcon->showMessage(tr("New request"), tr("A new chat has been transferred to you. To start the chat click me."),icon , 15 * 1000);
    }

    this->chatID = chat_id;
    this->chatMode = chat_mode;

    if ( QFile::exists(qApp->applicationDirPath() + "/sounds/new_chat.mp3") ) {
        this->mediaObject->setCurrentSource(Phonon::MediaSource(qApp->applicationDirPath() + "/sounds/new_chat.mp3"));
        this->mediaObject->play();
    }

}

void MainWindow::messageClicked()
{
    if (this->chatID != 0)
    {
        // Pending chat
        if (this->chatMode == 0)
        {
            ChatWindow *crw = new ChatWindow(this->chatID);
            crw->show();
        }

        // Transfered chat
        if (this->chatMode == 1)
        {
            LhcWebServiceClient::instance()->LhcSendRequest("/xml/accepttransferbychat/"+QString::number(this->chatID));
            ChatWindow *crw = new ChatWindow(this->chatID);
            crw->show();
        }
    }
}

/**
* Currently not used :)
* Will be used in future releases
*/
void MainWindow::createStatusBar()
{
	statusLabel = new QLabel(tr("Waiting for action..."));
	statusBar()->addWidget(statusLabel);
}

void MainWindow::iconActivated(QSystemTrayIcon::ActivationReason reason)
{

    switch (reason) {
    case QSystemTrayIcon::Trigger:
		// Show window if it's hidden hide if it is shown.
		if (this->isHidden())
		{
			this->showNormal();
			this->activateWindow();
		}
		else
			this->hide();
	break;
	/* FOR FEATURE
    case QSystemTrayIcon::DoubleClick:
        iconComboBox->setCurrentIndex((iconComboBox->currentIndex() + 1)
                                      % iconComboBox->count());
        break;
    case QSystemTrayIcon::MiddleClick:
        showMessage();
        break;
	*/
    default:
        ;
    }
}


/**
* @brief Create try icon actions
*/
void MainWindow::createActions()
{
    restoreAction = new QAction(tr("&Restore"), this);
    connect(restoreAction, SIGNAL(triggered()), this, SLOT(showNormal()));
    restoreAction->setIcon(QIcon(":/images/application_get.png"));

    quitAction = new QAction(tr("&Quit"), this);
    quitAction->setIcon(QIcon(":/images/close.png"));
    connect(quitAction, SIGNAL(triggered()), qApp, SLOT(quit()));
	/**
	* Main menu actions
	*/
	exitAct = new QAction(tr("E&xit"), this);
    exitAct->setShortcut(tr("Ctrl+Q"));
    exitAct->setIcon(QIcon(":/images/close.png"));
    exitAct->setStatusTip(tr("Exit the application"));
    connect(exitAct, SIGNAL(triggered()), this, SLOT(close()));

    connectionAct = new QAction(tr("Login as"), this);
    connectionAct->setStatusTip(tr("Login as"));
    connectionAct->setShortcut(tr("Ctrl+U"));
    connect(connectionAct, SIGNAL(triggered()), this, SLOT(changeConnection()));

    chatroomsAct = new QAction(tr("Chat rooms"), this);
    chatroomsAct->setStatusTip(tr("Chat rooms"));
    chatroomsAct->setShortcut(tr("Ctrl+R"));
    connect(chatroomsAct, SIGNAL(triggered()), this, SLOT(chatRooms()));

    onlineofflineAct = new QAction(tr("I'm online"), this);
    onlineofflineAct->setStatusTip(tr("Change status to online"));
    onlineofflineAct->setShortcut(tr("Ctrl+O"));
    onlineofflineAct->setCheckable(true);
    connect(onlineofflineAct, SIGNAL(triggered()), this, SLOT(chatOnlineStatus()));

    aboutAct = new QAction(tr("About"), this);
    aboutAct->setStatusTip(tr("About the program"));
    connect(aboutAct, SIGNAL(triggered()), this, SLOT(about()));

}

void MainWindow::chatRooms()
{
    ChatRoomsWindow *crw = new ChatRoomsWindow(this);
	mdiArea->addSubWindow(crw);
    crw->setMdiAreas(mdiArea);
	crw->show();
}

void MainWindow::chatOnlineStatus()
{
    if (this->onlineofflineAct->isChecked()) {
        LhcWebServiceClient::instance()->LhcSendRequest("/xml/setonlinestatus/0");
    } else {
        LhcWebServiceClient::instance()->LhcSendRequest("/xml/setonlinestatus/1");
    }
}

void MainWindow::changeConnection()
{
    LoginDialog *lgnDialog = new LoginDialog();
   lgnDialog->exec();
}

void MainWindow::about()
{
    QMessageBox box(this);
    box.setTextFormat(Qt::RichText);
    box.setText(tr("<center>Live helper chat</center> <p>System purpose is to give Live helper chat desktop interface.</p><p>This is 1.57 version of desktop client.</p>"));
    box.setWindowTitle(QApplication::translate("AboutDialog", "For web app since 1.57v Live Helper Chat version."));
    box.setIcon(QMessageBox::NoIcon);
    box.exec();
}

/**
* @brief Create system try icon and assign menu
*/
void MainWindow::createTrayIcon()
{
    trayIconMenu = new QMenu(this);
    trayIconMenu->addAction(restoreAction);
    trayIconMenu->addAction(quitAction);

    trayIcon = new QSystemTrayIcon(this);
    trayIcon->setIcon(QIcon(":/images/icon.png"));
    trayIcon->setContextMenu(trayIconMenu);
}

/**
* @brief Create main menu
*/
void MainWindow::createMainMenu()
{
	mainMenu = menuBar()->addMenu(tr("&Actions"));
    mainMenu->addSeparator();
    mainMenu->addAction(exitAct);

    chatMenu = menuBar()->addMenu(tr("&Chats"));
    chatMenu->addAction(chatroomsAct);
    chatMenu->addAction(onlineofflineAct);

	managementMenu = menuBar()->addMenu(tr("&Management"));
    managementMenu->addAction(connectionAct);

    helpMenu =  menuBar()->addMenu(tr("&Help"));
    helpMenu->addAction(aboutAct);
}


/**
* @brief minimize on close
*/
void MainWindow::closeEvent(QCloseEvent *event)
{
	hide();
	event->ignore();
}
