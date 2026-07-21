import AddonProElement from "@/components/dashboard/AddonProElement";
import AffiliateProgram from "@/components/dashboard/AffiliateProgram";
import ConnectWithUs from "@/components/dashboard/ConnectWithUs";
import Documentation from "@/components/dashboard/Documentation";
import HeroBanner from "@/components/dashboard/HeroBanner";
import LatestBlog from "@/components/dashboard/LatestBlog";
import RecoPlugins from "@/components/dashboard/RecoPlugins";
import RequestFeatureForm from "@/components/dashboard/RequestFeatureForm";
import Tutorial from "@/components/dashboard/Tutorial";
import VideoBanner from "@/components/dashboard/VideoBanner";
import WhatsNew from "@/components/dashboard/WhatsNew";
import QuickAccess from "@/components/dashboard/QuickAccess";
import SplitRow from "@/components/shared/SplitRow";

const Dashboard = () => {

  return (
    <div className="flex flex-col gap-6">
      <SplitRow columns={[60, 40]}>
        <HeroBanner />
        <VideoBanner />
      </SplitRow>

      {/* <div className="mt-2">
        <QuickAccess />
      </div> */}
      <SplitRow columns={[60, 40]}>
        <Tutorial />
        <Documentation />
      </SplitRow>
      {/* <div className="grid grid-cols-2 xl:grid-cols-3 gap-6 h-full">
        <AddonProElement />
        <RecoPlugins />
      </div> */}
      <SplitRow columns={[60, 40]}>
        <div className="flex flex-col gap-6">
          <AffiliateProgram />
          <WhatsNew />
        </div>
        <RequestFeatureForm />
      </SplitRow>
      {/* <ConnectWithUs /> */}
      <LatestBlog />
    </div>
  );
};

export default Dashboard;

