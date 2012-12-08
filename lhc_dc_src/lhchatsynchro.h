#ifndef LHCHATSYNCHRO_H
#define LHCHATSYNCHRO_H

#include <QPointer>
#include <QMap>
#include <QScriptEngine>
#include "objectfactory.h"
#include <QtGui>

// Used quarded pointer to avoid dandling pointers.
// @see http://doc.trolltech.com/4.5/qpointer.html

struct ChatSynchro {
    void (*pt2Function)(void* pt2Object, QScriptValue);
    QPointer<QObject> pt2Object;
    // Last synchronized message
    int msg_id;
};

/**
* First item is chat ID.
* It stores list of ChatSynchro items witch is under synchronization.
**/
typedef QMap<int, QList<ChatSynchro> > chatSynchroContainer;

/**
* Request looks like
* chats[chat_id]['last_message_id']
* chats[chat_id]['last_message_2']
* last Message
**/

class LhcChatSynchro : public ObjectFactory
{
Q_OBJECT

public:	
    static LhcChatSynchro* instance();

	chatSynchroContainer chatsToSynchro; // Que used to store chats under synchronization

    void addChatToSynchro(int chat_id, int msg_id, QObject* pt2Object, void (*pt2Function)(void* pt2Object, QScriptValue));

    

    static void receivedMessages(void* pt2Object, QByteArray result);

    // Synchronize timer
    QTimer *timer;
    public slots:
        void sendRequest();

protected:    
        LhcChatSynchro();


};

#endif
