#ifndef PRIVATEMESSAGE_H
#define PRIVATEMESSAGE_H

#include <QDialog>

namespace Ui {
class privatemessage;
}

class privatemessage : public QDialog
{
    Q_OBJECT
    
public:
    explicit privatemessage(QWidget *parent = 0);
    ~privatemessage();
    void setVisitorID(int id);
private slots:
    void pushButtonClicked();
    void pushButton2Clicked();
    
private:
    Ui::privatemessage *ui;
    int chat_id;
};

#endif // PRIVATEMESSAGE_H
