#ifndef ONLINEUSERINFO_H
#define ONLINEUSERINFO_H

#include <QDialog>
 #include <QTableWidget>

namespace Ui {
class onlineuserinfo;
}

class onlineuserinfo : public QDialog
{
    Q_OBJECT
    
public:
    explicit onlineuserinfo(QWidget *parent = 0,int chat_id=0);
    ~onlineuserinfo();
    void setVisitorID(int id);

    static void getVisitorData(void* pt2Object, QByteArray result);
private slots:
    void pushButtonClicked();


    
private:
    Ui::onlineuserinfo *ui;
    int chat_id;
    QTableWidget
        *filesTable;


};



#endif // ONLINEUSERINFO_H
