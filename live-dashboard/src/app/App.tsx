import React, { ReactElement, FC } from 'react';
import RouterOutlet from '@/src/router/RouterOutlet';

type Props = Record<string, never>;

const App: FC<Props> = (): ReactElement => {
  return (
    <div className="ebs-dashboard-wrapper">
     <RouterOutlet />
    </div>
  );
};

export default App;