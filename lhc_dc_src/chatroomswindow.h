#ifndef CHATROOMSWINDOW_H
#define CHATROOMSWINDOW_H

#include <QtGui>
#include "ui_chatroomswidget.h"
#include "mainwindow.h"

class LHQTableWidget;

class ChatRoomsWindow : public QWidget
{
    Q_OBJECT

public:
    ChatRoomsWindow( MainWindow *parent = 0);
    ~ChatRoomsWindow();
    void setMdiAreas(QMdiArea *mdiMainArea = 0);


private slots:

    // Main menus
    void pendingChatsMenu(QPoint p);
    void activeChatsMenu(QPoint p);
    void closedChatsMenu(QPoint p);
    void transferedChatsMenu(QPoint p);
    void onlineUsersMenu(QPoint p);

    // Pending actions
    void denyPendingChatAction();
    void acceptPendingChatWindow();
    void newPendingChatWindow();
    void newPendingSepChatWindow();

    // Active chats menu
    void addActiveChatWindow();
    void newActiveChatWindow();
    void newActiveSepChatWindow();
    void closeActiveChatAction();
    void deleteActiveChatAction();

    // Online Users menu
    void sendMessageWindow();
    void userInfoWindow();

    // Closed chats menu
    void addClosedChatWindow();
    void newClosedChatWindow();
    void newCloseSepChatWindow();
    void deleteClosedChatAction();

    // Transfered chat menu
    void addTransferdChatWindow();
    void newTransferdChatWindow();
    void newTransferdSepChatWindow();

private:

     void createPendingChatsTab();
     void createClosedChatsTab();
     void createActiveChatsTab();
     void createOnlineUsersTab();

     QTabWidget *ChatRoomstabWidget;

     // Synchronize timer
     QTimer *timer;

     /**
     * List widgets
     */ 
     LHQTableWidget 
         *pendingChatsList,
         *closedChatsList,
         *transferedChatsList,
         *activeChatsList,
         *OnlineUsersList;


     QWidget  
         /**
         * Pending chats widget container
         */
         *tabPendingChats,
         
         /**
         * Closed chats container
         */
         *tabClosedChats,

         /**
         * Deleted chats
         */
         *tabActiveChats,

         /**
         * Online users
         */
         *tabOnlineUsers;

    MainWindow
         /**
         * Main window
         */
         *parentWidget;  


    QGroupBox 
        /**
        * Pending tab groupboxes
        */
        *pendingChatsGroupBox,
        *transferedChatsGroupBox,

        /**
        * Closed tab groupbox
        */
        *closedChatsGroupBox,

        /**
        * Active tab groupbox
        */
        *activeChatsGroupBox,

        /**
        * Online users tab groupbox
        */
        *OnlineUsersGroupBox;
   
    QVBoxLayout   
        /**
        * => General chats layouts
        * => Pending chats layouts
        * => Transfared chats layouts
        */
        *pendingDataVBOX,
        *pendingChatsListVBOX,
        *transferedChatsListVBOX,
        
        /**
        * Closed chats layouts      
        */
        *closedDataVBOX,
        *closedChatsListVBOX,

        /**
        * Deleted chats layouts      
        */
        *activeDataVBOX,
        *activeChatsListVBOX,

        /**
         * Online Users layouts
        */
        *OnlineUsersListVBOX,
        *OnlineUsersDataVBOX;

    // Midi area parent
    QMdiArea *mdiArea;

    bool balloonEnabled;
 
    static void receivedDataCallback(void* pt2Object, QByteArray result);

    private slots:
        // Synchronize all table lists.
        void synschronize();

protected:
    Ui::ChatRoomsWidget ui;

};

#endif
