#include <QApplication>
#include <QDebug>
#include <QStringList>
#include <QScriptEngine>
#include <QScriptValueIterator>

#include "lhchatsynchro.h"
#include "webservice.h"

//#define DEBUG


LhcChatSynchro *LhcChatSynchro::instance() {
static LhcChatSynchro* fac = 0;
if (fac == 0 ) {
    fac = new LhcChatSynchro();
		
    if (qApp != 0) { 
        try {
           fac->setParent(qApp); 		  
        }
        catch (...) {
            qDebug() << QString("%1 %2")
                        .arg("LHCWebServiceInstance::instance()")
                        .arg("failed to setParent");
        }
    }
}
return fac; 
};

void LhcChatSynchro::addChatToSynchro(int chat_id, int msg_id, QObject* pt2Object, void (*pt2Function)(void* pt2Object, QScriptValue))
{
    // Chat to Synchronate
    ChatSynchro chatToSynchro;
    chatToSynchro.pt2Function = pt2Function;
    chatToSynchro.pt2Object = pt2Object;
    chatToSynchro.msg_id = msg_id;

    if (this->chatsToSynchro.contains(chat_id))
    {
        this->chatsToSynchro[chat_id].append(chatToSynchro);
    } else {
        QList<ChatSynchro> initialList;
        initialList.append(chatToSynchro);
        this->chatsToSynchro.insert(chat_id,initialList);        
    }

    if (!timer->isActive())
    {
        //qDebug("Starting timer");
        this->sendRequest();
        timer->start();
    }
}

void LhcChatSynchro::sendRequest()
{
    //qDebug("Size chats - %d",this->chatsToSynchro.size());
    //qDebug("Size chats - %d",this->chatsToSynchro.value(8).size());
    QString query;
    QStringList chatmsgtoSynchro;
    QStringList requestString;
    query = "";

    QMapIterator<int, QList<ChatSynchro> > i( this->chatsToSynchro );
    while (i.hasNext()) {
         query = "";
         i.next();
         query += QString::number(i.key())+"|";         
         for (int msg = 0; msg < i.value().size(); ++msg) {
             chatmsgtoSynchro.append(QString::number(i.value().at(msg).msg_id));           
         }
         query += chatmsgtoSynchro.join(",");
         requestString.append(query);
         chatmsgtoSynchro.clear();
     }

    query = requestString.join(";");

    requestString.clear();
    requestString.append("chats="+query);

    LhcWebServiceClient::instance()->LhcSendRequest(requestString,"/xml/chatssynchro/",(QObject*) this, LhcChatSynchro::receivedMessages);
}


void LhcChatSynchro::receivedMessages(void* pt2Object, QByteArray result)
{
    QScriptValue sc;
    QScriptEngine engine;

    sc = engine.evaluate("("+QString(result)+")");
    //qDebug("Result %s",QString(result).toStdString().c_str());

    if (sc.property("error").toBoolean() == false)
    {
         LhcChatSynchro* mySelf = (LhcChatSynchro*) pt2Object;
         QMap <int,QMap <int,QScriptValue> > messagesAppend;
         QMap <int,QScriptValue> tmpMessageList,chatStatusInfo; 

         /**
         * Creates initial mapping. 
         * Stores chats and synchronized lists.   
         **/
         QScriptValueIterator it(sc.property("result"));
         while (it.hasNext()) {
             it.next(); // Points to chat

             if (it.flags() & QScriptValue::SkipInEnumeration)
                      continue;

             QScriptValueIterator itt(it.value().property("messages"));
             while (itt.hasNext()) {
                    itt.next();

                    if (itt.flags() & QScriptValue::SkipInEnumeration)
                             continue;

                    tmpMessageList.insert(itt.name().toInt(),itt.value());                    
             }

             messagesAppend.insert(it.name().toInt(),tmpMessageList);

             // Perhaps we have some chat information.
             if (!it.value().property("chat_status").toString().isEmpty())
                chatStatusInfo.insert(it.name().toInt(),it.value().property("chat_status"));

             tmpMessageList.clear(); 
         }
                

        QMapIterator<int, QList<ChatSynchro> > i(mySelf->chatsToSynchro);
            
        while (i.hasNext()) {      
             i.next();
             //query += QString::number(i.key())+"|"; //Chat ID
                          for (int msg = 0; msg < i.value().size(); ++msg) {
                 //If receiver object exits
                 if (i.value().at(msg).pt2Object)
                 {               
                    if (messagesAppend.contains(i.key()))
                    {
                        //qDebug("Chat received data %d",i.key());
                        if (messagesAppend.value(i.key()).contains(i.value().at(msg).msg_id))
                        {                    
                            // Call callback and pass script value
                            i.value().at(msg).pt2Function(i.value().at(msg).pt2Object,messagesAppend.value(i.key()).value(i.value().at(msg).msg_id));
                            
                            // Just get and set last message ID
                            QScriptValueIterator itt(messagesAppend.value(i.key()).value(i.value().at(msg).msg_id));
                            itt.toBack();
                            itt.previous();

                            if (itt.flags() & QScriptValue::SkipInEnumeration)
                                    itt.previous();

                            mySelf->chatsToSynchro[i.key()][msg].msg_id = itt.value().property("id").toString().toInt();
                           
                        }
                    }

                    // We have some status information
                    if (chatStatusInfo.contains(i.key()))
                    {
                        i.value().at(msg).pt2Function(i.value().at(msg).pt2Object,chatStatusInfo.value(i.key()));
                    }
                 }                                       
             }           
        }

        //Reloop and delete destroyed pointers and if needed chats.
        i.toFront();
        while (i.hasNext()) {      
            i.next();
            for (int msg = 0; msg < i.value().size(); ++msg) {
                 //If receiver object exits
                 if (!i.value().at(msg).pt2Object)
                 {  
                    //qDebug("Object destroyed - %d",msg);
                    mySelf->chatsToSynchro[i.key()].removeAt(msg);  
                 }
            }
            
            if (mySelf->chatsToSynchro[i.key()].size() == 0) {
                 mySelf->chatsToSynchro.remove(i.key());
                 //qDebug("Chat removed %d",i.key());
             }
        }

        if (mySelf->chatsToSynchro.size() == 0)
        {
            //qDebug("Empty chats lists Stopping timer");
            mySelf->timer->stop();
        }

    } else {
        //qDebug("There was error while chat synchronising...");
    }

}

LhcChatSynchro::LhcChatSynchro()
{

timer = new QTimer(this);
connect(timer, SIGNAL(timeout()), this, SLOT(sendRequest()));
timer->setInterval(4000);

}
