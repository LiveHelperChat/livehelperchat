#ifndef LHCTEXTEDIT_H
#define LHCTEXTEDIT_H
#include <QTextEdit>


class LHCTextEdit : public QTextEdit
{
    Q_OBJECT

public:
    LHCTextEdit(int chat_id,QWidget *parent = 0)
        : QTextEdit(parent)
    {
        // Without this event filter wont work.
        this->installEventFilter(this);
        this->chatID = chat_id;
        this->messageSend = false;
    };

    bool messageSend;

    void getCurrentChat();
    void sendMessage();

    ~LHCTextEdit();

protected:
    bool eventFilter(QObject *obj, QEvent *event);

private:
    int chatID;

    
   
};

#endif
