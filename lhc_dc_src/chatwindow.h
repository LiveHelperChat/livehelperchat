#ifndef CHATWINDOW_H
#define CHATWINDOW_H

#include <QtGui>
#include <QScriptEngine>

#include <phonon/audiooutput.h>
#include <phonon/mediaobject.h>

#include "ui_chatwidget.h"
#include "lhctextedit.h"

class ChatWindow : public QWidget
{
    Q_OBJECT

public:
    ChatWindow(int chat_id, QWidget *parent = 0);
    ~ChatWindow();
    void setMdiAreas(QMdiArea *mdiMainArea = 0);
    static void getDataChat(void* pt2Object, QByteArray result);
    static void receivedMessages(void* pt2Object, QScriptValue result);
    
    void setIsTabMode(bool tabMode);
    void setTabIndex(int index,QTabWidget *widget);

    private slots:
        void closeButtonClicked();
        void closeChatClicked();
        void deleteChatClicked();
        void separateWindowClicked();
        void transferChatClicked();
        void stateChanged(Phonon::State newState, Phonon::State oldState);

private:
   
    QTabWidget *chatRoomsParent;

    int chatID,tabIndex;

    QString clientNick;

    // Midi area parent
    QMdiArea *mdiArea;

    QGridLayout 
        *mainGrindLayout;

    QGroupBox 
        /**
        * Action groups
        */
        *actionsGroupBox,

        *informationGroupBox,

        *ownerGroupBox;

    QTextEdit 
        *messagesText;
   

    LHCTextEdit *newmessageText;

    QLabel *inforChat,*infoOwner;

    QVBoxLayout *infoGroupBoxLayout,*infoOwnerGroupBoxLayout;

    QHBoxLayout *actionButtonsLayout;


    QPushButton *closeDialogButton,*closeChatButton,*deleteChatButton,*transferChatButton,*separateWindowButton;

    bool asTab,separateWindow;
    
    Phonon::MediaObject *mediaObject;
    Phonon::AudioOutput *audioOutput;



protected:
    Ui::ChatWidget ui;

};

#endif
