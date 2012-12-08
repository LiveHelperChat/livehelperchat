#ifndef LTQTABLEWIDGET_H
#define LTQTABLEWIDGET_H

#include <QTableWidget>
#include "webservice.h"

class QScriptValue;

class LHQTableWidget : public QTableWidget
{
    Q_OBJECT

public:
    
    

    LHQTableWidget(QWidget *parent = 0)
        : QTableWidget(parent)
    {
       lastChatID = 0;
       tableMode = 0;
    };
  
    int tableMode;

    ~LHQTableWidget();

    inline void setTableMode(int tableMode){this->tableMode = tableMode;};

    void setQuery(QString query);
    static void resultCallback(void* pt2Object, QByteArray result);
    void setData(QScriptValue sc);
    int getCurrentChat();
    int getColumnByName(QString);


    int lastChatID;
    
    QMap<int, int> rowChatID;
    QMap<QString, int> mapColumnsPosition;


    signals:
       void newChatAdded(int chat_id,int chat_mode);
};

#endif
