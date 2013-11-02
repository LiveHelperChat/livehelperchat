#include <QScriptEngine>
#include <QScriptValueIterator>
#include <QtGui>

#include "lhtablewidget.h"
#include "timeeditdelegate.h"

LHQTableWidget::~LHQTableWidget()
{
    
}

void LHQTableWidget::setData(QScriptValue sc)
{
    this->setRowCount(sc.property("size").toInteger());
    
    // If more than one
    if (sc.property("size").toInteger() > 0)
    {            
            QScriptValueIterator itcolumnsnames(sc.property("column_names"));
            
            QMap <QString,QString> columnsLiteral;
            while (itcolumnsnames.hasNext()) {
                itcolumnsnames.next();

                if (itcolumnsnames.flags() & QScriptValue::SkipInEnumeration)
                        continue;

                columnsLiteral.insert(itcolumnsnames.name(),itcolumnsnames.value().toString());
            }

            QScriptValueIterator itcolumns(sc.property("rows"));
            itcolumns.next();
      
            QScriptValueIterator itcolumnsItems(itcolumns.value());

            int columnPosition = 0;

            mapColumnsPosition.clear();
            //Find out columns
            QStringList columns;
            QStringList columnsTranslated;

            while (itcolumnsItems.hasNext()) {
                itcolumnsItems.next();

                if (itcolumnsItems.flags() & QScriptValue::SkipInEnumeration)
                        continue;

                columns.append(itcolumnsItems.name());
                columnsTranslated.append(columnsLiteral.contains(itcolumnsItems.name()) ? tr(columnsLiteral.value(itcolumnsItems.name()).toUtf8()) : itcolumnsItems.name() );
                mapColumnsPosition.insert(itcolumnsItems.name(), columnPosition);
                columnPosition++;
            }

            // Set columns count
            this->setColumnCount(columns.size());

            // User can change if he wants
            this->setHorizontalHeaderLabels(columnsTranslated);

            //Fill matrix
            int row = 0;
            int tmpLastChatID = 0;

            itcolumns.toFront(); 
            while (itcolumns.hasNext()) {
                 itcolumns.next(); 
                
                 if (itcolumns.flags() & QScriptValue::SkipInEnumeration)
                          continue;

                for (int i = 0; i < columns.size(); ++i)
                {               
                    this->setItem(row,i,new QTableWidgetItem(itcolumns.value().property(columns.at(i)).toString()));

                    //If new chat added. Like it always will have bigger ID than previous
                    if (i == 0)
                    {
                        rowChatID.insert(row,itcolumns.value().property(columns.at(i)).toInteger());
                        tmpLastChatID = (itcolumns.value().property(columns.at(i)).toInteger()) > tmpLastChatID ? itcolumns.value().property(columns.at(i)).toInteger() : tmpLastChatID;
                    }
                }
                row++;
            } 
            
            QScriptValueIterator hiddencolumns(sc.property("hidden_columns"));
            while (hiddencolumns.hasNext()) {
                hiddencolumns.next();

                if (hiddencolumns.flags() & QScriptValue::SkipInEnumeration)
                         continue;

                setColumnHidden(mapColumnsPosition.value(hiddencolumns.value().toString()),true);
            }

            // Columns timestamp delegates
            QScriptValueIterator tscolumns(sc.property("timestamp_delegate"));
            while (tscolumns.hasNext()) {
                tscolumns.next();

                if (tscolumns.flags() & QScriptValue::SkipInEnumeration)
                         continue;

                this->setItemDelegateForColumn(mapColumnsPosition.value(tscolumns.value().toString()),new TimeEditDelegate("yyyy.MM.dd hh:mm:ss"));             
            }



            //Emit signal if needed
            //if (tmpLastChatID != lastChatID) {
            //    lastChatID = tmpLastChatID;
                emit newChatAdded(tmpLastChatID,tableMode);
            //}
    }
}

int LHQTableWidget::getCurrentChat()
{
    if (rowChatID.contains(this->currentRow()))
        return rowChatID.value(this->currentRow());
    else
        return 0;
}

int LHQTableWidget::getColumnByName(QString columnName)
{
    return mapColumnsPosition.value(columnName);
}

void LHQTableWidget::resultCallback(void* pt2Object, QByteArray result)
{
    LHQTableWidget* mySelf = (LHQTableWidget*) pt2Object;

    QScriptValue sc; 
    QScriptEngine engine;
    sc = engine.evaluate("("+QString(result)+")");

    mySelf->setRowCount(sc.property("size").toInteger());
    
    // If more than one
    if (sc.property("size").toInteger() > 0)
    {
            QScriptValueIterator itcolumns(sc.property("rows"));
            itcolumns.next();                 

            QScriptValueIterator itcolumnsItems(itcolumns.value());

            //Find out columns
            QStringList columns;
            while (itcolumnsItems.hasNext()) {
                itcolumnsItems.next();

                if (itcolumnsItems.flags() & QScriptValue::SkipInEnumeration)
                         continue;

                columns.append(itcolumnsItems.name());
            }

            // Set columns count
            mySelf->setColumnCount(columns.size());

            // User can change if he wants
            mySelf->setHorizontalHeaderLabels(columns);

            //Fill matrix
            int row = 0;
            int tmpLastChatID = 0;
            itcolumns.toFront(); 
            while (itcolumns.hasNext()) {
                 itcolumns.next(); 

                 if (itcolumns.flags() & QScriptValue::SkipInEnumeration)
                          continue;


                for (int i = 0; i < columns.size(); ++i)
                {               
                    mySelf->setItem(row,i,new QTableWidgetItem(itcolumns.value().property(columns.at(i)).toString()));

                    if (i == 0)
                        tmpLastChatID = (itcolumns.value().property(columns.at(i)).toInteger()) > tmpLastChatID ? itcolumns.value().property(columns.at(i)).toInteger() : tmpLastChatID;
                }
                row++;
             } 

            //Emit signal if needed
            if (tmpLastChatID != mySelf->lastChatID) {                
                mySelf->lastChatID = tmpLastChatID;
                emit mySelf->newChatAdded(mySelf->lastChatID,mySelf->tableMode);
            }
    }    
}

void LHQTableWidget::setQuery(QString query)
{
    LhcWebServiceClient *lhwsc =LhcWebServiceClient::instance();
    lhwsc->LhcSendRequest(query,(QObject*) this, LHQTableWidget::resultCallback);	
};
