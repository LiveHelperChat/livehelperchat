#ifndef MAIN_WINDOW_H
#define MAIN_WINDOW_H

#include <QMainWindow>
#include <QList>
#include <QSystemTrayIcon>
#include <QLabel>
#include <QtGui>
#include <qaction.h>

#include <phonon/audiooutput.h>
#include <phonon/mediaobject.h>

class MainWidget;
class LoginDialog;
class QMdiArea;

class MainWindow : public QMainWindow
{
    Q_OBJECT
public:
    MainWindow();
    static void parseOnlineStatus(void* pt2Object, QByteArray result);

    /**
    * Change online offline action
    */
    QAction *onlineofflineAct;

protected:
    void closeEvent(QCloseEvent *event);

public slots:
    void showToolTipNewChat(int chat_id, int chat_mode);

private slots:
	void iconActivated(QSystemTrayIcon::ActivationReason reason);
	void ChangeStatusBar(const QString &newStatus);
    void messageClicked();
    
    /**
    * Connection management
    */
    void changeConnection();
  
    /**
    * Chat menu actions
    */
    void chatRooms();

    /**
    * Change online offline status
    */
    void chatOnlineStatus();

    /**
    * About action
    */
    void about();
       
    
private:

	/**
	* Crate menu actions
	*/
	void createActions();
	void createTrayIcon();
	void createStatusBar();
	void createMainMenu();
    void getOnlineStatus();

	/**
	* System try icon
	*/
	QSystemTrayIcon *trayIcon;

	/**
	* Try icon menu
	*/
	QMenu *trayIconMenu;

	/**
	* Top menu items
	*/
	QMenu *mainMenu;
	QMenu *managementMenu;
    QMenu *chatMenu;

    /**
    * Sub menus
    */
    QMenu *helpMenu;
   
   
	/** 
	* Main menu actions
	*/
	QAction *exitAct;
    QAction *aboutAct;

    /** 
	* Management menu actions
	*/
    QAction *connectionAct;

    /** 
	* Chat menu actions
	*/
    QAction *chatroomsAct;


	/**
	* Try icon actions
	*/
	QAction *restoreAction;
    QAction *quitAction;

	/**
	* Status bar
	*/
	QLabel *statusLabel;

	/**
	* Create main widget
	*/
	MainWidget *mainwidget;

	/** 
	* Login dialog
	*/
	LoginDialog *lgnDialog;


	/**
	* Mdi area for windows 
	*/
	QMdiArea *mdiArea;

    // Hold properties then message is shown and property from table.

    int chatID;
    int chatMode;

    Phonon::MediaObject *mediaObject;
    Phonon::AudioOutput *audioOutput;

}; 

#endif
