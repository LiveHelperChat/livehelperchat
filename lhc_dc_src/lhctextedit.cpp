#include <QDebug>
#include <QEvent>
#include <QKeyEvent>

#include "lhctextedit.h"
#include "webservice.h"
#include "lhchatsynchro.h"

LHCTextEdit::~LHCTextEdit()
{

}


bool LHCTextEdit::eventFilter( QObject *o, QEvent *e )
{
    //qDebug("Event filter");
    switch ( e->type( ) )
    {
    case QEvent::KeyPress:
    {
        QKeyEvent *ke = (QKeyEvent*)e;
        if ( (ke->key( ) == Qt::Key_Enter || ke->key( ) == Qt::Key_Return) && !(ke->modifiers() & Qt::ShiftModifier))
        {

            if (!this->toPlainText().isEmpty())
            {
                QStringList requestString;
                requestString.append("msg="+this->toPlainText());
                LhcWebServiceClient::instance()->LhcSendRequest(requestString,"/xml/addmsgadmin/"+QString::number(this->chatID)); 

                this->messageSend = true;

                //Update instantly
                LhcChatSynchro::instance()->sendRequest();

                this->clear();
            }
            
            return true;
        }
    } break ;
    default:
        break ;
    }

    return QTextEdit::eventFilter( o, e );
}
